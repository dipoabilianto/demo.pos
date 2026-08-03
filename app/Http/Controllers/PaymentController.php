<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\CustomerObject;
use Xendit\Invoice\InvoiceApi;

class PaymentController extends Controller
{
    private const XENDIT_IPS = [
        '52.76.126.98', '52.76.127.134', '54.254.159.96',
        '54.254.163.152', '52.77.235.224', '52.77.236.192',
        '18.141.20.152', '18.141.20.184',
    ];

    public function __construct(
        private SettingService $settingService,
        private PaymentService $paymentService,
    ) {}

    private function getXenditSecretKey(): ?string
    {
        $secretKey = config('services.xendit.secret_key');
        if (! $secretKey) {
            $settings = $this->settingService->getSettings();
            $secretKey = $settings['xendit_secret_key'] ?? '';
        }

        return $secretKey ?: null;
    }

    public function checkout(Sale $sale): View
    {
        $sale->load('items');
        $paymentMethods = PaymentMethod::active()->get();

        return view('payments.checkout', compact('sale', 'paymentMethods'));
    }

    public function createInvoice(Request $request, Sale $sale): JsonResponse|RedirectResponse
    {
        if ($sale->payment_status === 'paid') {
            return response()->json(['error' => 'Invoice ini sudah dibayar.'], 422);
        }

        $activeCodes = PaymentMethod::active()->pluck('code')->implode(',');
        $request->validate([
            'payment_method' => 'required|string|in:'.$activeCodes,
        ]);

        $secretKey = $this->getXenditSecretKey();
        if (! $secretKey) {
            return response()->json(['error' => 'Xendit API key belum dikonfigurasi.'], 500);
        }

        try {
            Configuration::setXenditKey($secretKey);

            $externalId = 'SALE-'.$sale->id.'-'.time();

            $customerObj = new CustomerObject;
            $customerObj->setGivenNames('Customer');
            $customerObj->setSurname('Oribun');
            $customerObj->setEmail('customer@oribun.app');

            $createRequest = new CreateInvoiceRequest;
            $createRequest->setExternalId($externalId);
            $createRequest->setAmount((float) $sale->total);
            $createRequest->setDescription('Pembayaran Invoice '.$sale->invoice_number);
            $createRequest->setInvoiceDuration(86400);
            $createRequest->setCustomer($customerObj);
            $createRequest->setPaymentMethods([$request->payment_method]);
            $createRequest->setCurrency('IDR');

            $apiInstance = new InvoiceApi;
            $invoice = $apiInstance->createInvoice($createRequest);

            Transaction::create([
                'xendit_id' => $invoice['id'],
                'type' => 'sale',
                'transactionable_type' => Sale::class,
                'transactionable_id' => $sale->id,
                'payment_channel' => $request->payment_method,
                'status' => 'pending',
                'amount' => $sale->total,
                'external_id' => $externalId,
                'xendit_response' => $invoice,
                'branch_id' => $sale->branch_id ?? session('branch_id'),
            ]);

            $sale->update(['payment_method' => $request->payment_method]);

            return response()->json([
                'invoice_url' => $invoice['invoice_url'],
                'xendit_id' => $invoice['id'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat invoice. Silakan coba lagi.'], 500);
        }
    }

    public function webhook(Request $request): Response
    {
        $ip = $request->ip();
        if (! in_array($ip, self::XENDIT_IPS)) {
            Log::warning('Xendit webhook from untrusted IP', ['ip' => $ip]);

            return response('Forbidden', 403);
        }

        $callbackToken = $request->header('x-callback-token');
        if (! $callbackToken) {
            Log::warning('Xendit webhook missing callback token', ['ip' => $ip]);

            return response('Missing callback token', 401);
        }

        $webhookSecret = config('services.xendit.webhook_secret');
        if (! $webhookSecret) {
            $settings = $this->settingService->getSettings();
            $webhookSecret = $settings['xendit_webhook_secret'] ?? '';
        }
        if (! $webhookSecret) {
            Log::warning('Xendit webhook secret not configured');

            return response('Webhook not configured', 500);
        }

        $webhookId = $request->header('xendit-webhook-id');
        $signature = $request->header('xendit-webhook-signature');
        $timestamp = $request->header('xendit-webhook-timestamp');

        $verified = false;

        if ($webhookId && $signature && $timestamp && $webhookSecret) {
            try {
                $payload = $webhookId . $timestamp . $webhookSecret;
                $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
                if (hash_equals($expectedSignature, $signature)) {
                    $verified = true;
                } else {
                    Log::warning('Xendit webhook invalid HMAC signature', ['webhook_id' => $webhookId]);

                    return response('Invalid signature', 401);
                }
            } catch (\Exception $e) {
                Log::warning('Xendit webhook HMAC verification failed', ['error' => $e->getMessage()]);

                return response('Invalid signature', 401);
            }
        } elseif (hash_equals($webhookSecret, $callbackToken)) {
            $verified = true;
        }

        if (! $verified) {
            Log::warning('Xendit webhook verification failed', ['ip' => $ip]);

            return response('Invalid signature', 401);
        }

        try {
            $payload = $request->all();

            if (! isset($payload['external_id'])) {
                return response('OK', 200);
            }

            DB::transaction(function () use ($payload) {
                $transaction = Transaction::where('external_id', $payload['external_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $transaction || $transaction->status !== 'pending') {
                    return;
                }

                $status = match ($payload['status'] ?? '') {
                    'PAID', 'SETTLED' => 'success',
                    'EXPIRED' => 'expired',
                    'FAILED' => 'failed',
                    default => 'pending',
                };

                $transaction->update([
                    'status' => $status,
                    'xendit_response' => $payload,
                ]);

                if ($status === 'success') {
                    if ($transaction->type === 'sale') {
                        $sale = Sale::find($transaction->transactionable_id);
                        if ($sale) {
                            $sale->update(['payment_status' => 'paid']);
                        }
                    } elseif ($transaction->type === 'order') {
                        $order = Order::find($transaction->transactionable_id);
                        if ($order && ! in_array($order->payment_status, ['paid', 'success'])) {
                            $order->update(['payment_status' => 'success']);
                            $order->transitionStatus('processing');
                            $order->load('items');
                            $this->paymentService->createSaleFromOrder($order);
                        }
                    }
                } elseif (in_array($status, ['failed', 'expired']) && $transaction->type === 'order') {
                    $order = Order::find($transaction->transactionable_id);
                    if ($order && $order->payment_status === 'pending') {
                        $order->update(['payment_status' => $status]);
                        $order->transitionStatus('cancelled');
                    }
                }
            });

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Xendit webhook processing error', ['error' => $e->getMessage()]);

            return response('Error', 500);
        }
    }

    public function status(Transaction $transaction): JsonResponse
    {
        try {
            $secretKey = $this->getXenditSecretKey();
            if (! $secretKey) {
                return response()->json(['error' => 'Xendit API key belum dikonfigurasi.'], 500);
            }

            Configuration::setXenditKey($secretKey);
            $apiInstance = new InvoiceApi;
            $xenditInvoice = $apiInstance->getInvoiceById($transaction->xendit_id);

            return response()->json($xenditInvoice);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil status pembayaran.'], 500);
        }
    }
}
