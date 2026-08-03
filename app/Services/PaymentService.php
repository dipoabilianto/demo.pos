<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\CustomerObject;
use Xendit\Invoice\InvoiceApi;

class PaymentService
{
    public function __construct(
        private SettingService $settingService,
    ) {}

    public function calculateTax(float $subtotal, array $settings): float
    {
        if (! ($settings['tax_enabled'] ?? false)) {
            return 0;
        }
        $rate = ($settings['tax_rate'] ?? 0) / 100;
        return ($settings['tax_type'] ?? 'exclude') === 'include'
            ? $subtotal - ($subtotal / (1 + $rate))
            : $subtotal * $rate;
    }

    public function getSettings(): array
    {
        return $this->settingService->getSettings();
    }

    public function createSaleFromOrder(Order $order): Sale
    {
        $sale = Sale::create([
            'subtotal' => $order->subtotal,
            'discount' => $order->discount ?? 0,
            'tax' => $order->tax ?? 0,
            'total' => $order->total,
            'payment_method' => $order->payment_method,
            'payment_status' => 'paid',
            'notes' => $order->notes,
            'voucher_id' => $order->voucher_id,
            'voucher_code' => $order->voucher_code,
            'branch_id' => $order->branch_id ?? session('branch_id'),
            'business_type_id' => $order->business_type_id ?? session('business_type_id'),
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ]);

        foreach ($order->items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
            ]);
        }

        return $sale;
    }

    public function checkVoucherAvailability(string $code, int $branchId, float $subtotal): array
    {
        $voucher = Voucher::where('code', $code)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereNull('branch_id');
            })
            ->first();

        if (! $voucher || ! $voucher->isValidFor($subtotal)) {
            return ['valid' => false];
        }

        return [
            'valid' => true,
            'discount' => $voucher->calculateDiscount($subtotal),
            'type' => $voucher->type,
        ];
    }

    private function getXenditSecretKey(): ?string
    {
        $secretKey = config('services.xendit.secret_key');
        if (! $secretKey) {
            $settings = $this->getSettings();
            $secretKey = $settings['xendit_secret_key'] ?? '';
        }
        return $secretKey ?: null;
    }

    public function processCashOrTransferPayment(Order $order, string $paymentMethod, bool $isPublic): array
    {
        if (auth()->check() && ! $isPublic) {
            return DB::transaction(function () use ($order, $paymentMethod) {
                $lockedOrder = Order::withoutGlobalScopes()->where('id', $order->id)->lockForUpdate()->first();

                if (in_array($lockedOrder->payment_status, ['paid', 'success'])) {
                    return ['success' => false, 'error' => 'Pesanan ini sudah dibayar.'];
                }

                $lockedOrder->update([
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'paid',
                    'order_status' => 'processing',
                ]);

                $lockedOrder->load('items');
                $this->createSaleFromOrder($lockedOrder);

                return [
                    'success' => true,
                    'paid_directly' => true,
                    'payment_method' => $paymentMethod,
                ];
            });
        }

        $order->update([
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
        ]);

        return [
            'success' => true,
            'is_direct' => true,
            'payment_method' => $paymentMethod,
        ];
    }

    public function createXenditInvoice(Order $order, string $paymentMethod): array
    {
        $secretKey = $this->getXenditSecretKey();
        if (! $secretKey) {
            throw new \RuntimeException('Xendit API key belum dikonfigurasi.');
        }

        Configuration::setXenditKey($secretKey);

        $externalId = 'ORD-'.$order->id.'-'.time();

        $customerObj = new CustomerObject;
        $customerObj->setGivenNames($order->customer_name);
        $customerObj->setEmail($order->customer_email ?? 'customer@oribun.app');

        $createRequest = new CreateInvoiceRequest;
        $createRequest->setExternalId($externalId);
        $createRequest->setAmount((float) $order->total);
        $createRequest->setDescription('Pembayaran Pesanan '.$order->order_number);
        $createRequest->setInvoiceDuration(86400);
        $createRequest->setCustomer($customerObj);
        $createRequest->setPaymentMethods([$paymentMethod]);
        $createRequest->setCurrency('IDR');

        $apiInstance = new InvoiceApi;
        $invoice = $apiInstance->createInvoice($createRequest);

        Transaction::create([
            'xendit_id' => $invoice['id'],
            'type' => 'order',
            'transactionable_type' => Order::class,
            'transactionable_id' => $order->id,
            'payment_channel' => $paymentMethod,
            'status' => 'pending',
            'amount' => $order->total,
            'external_id' => $externalId,
            'xendit_response' => $invoice,
            'branch_id' => $order->branch_id ?? session('branch_id'),
        ]);

        return [
            'invoice_url' => $invoice['invoice_url'],
            'xendit_id' => $invoice['id'],
        ];
    }

    public function checkXenditStatus(string $xenditId): array
    {
        $secretKey = $this->getXenditSecretKey();
        if (! $secretKey) {
            return ['status' => Transaction::withoutGlobalScopes()->where('xendit_id', $xenditId)->value('status') ?? 'not_found'];
        }

        $transaction = Transaction::withoutGlobalScopes()->where('xendit_id', $xenditId)->first();

        if (! $transaction) {
            return ['status' => 'not_found'];
        }

        if ($transaction->status !== 'pending') {
            return ['status' => $transaction->status, 'payment_method' => $transaction->payment_channel];
        }

        try {
            Configuration::setXenditKey($secretKey);
            $apiInstance = new InvoiceApi;
            $xenditInvoice = $apiInstance->getInvoiceById($xenditId);
            $xenditStatus = $xenditInvoice['status'] ?? '';
        } catch (\Exception $e) {
            return ['status' => 'pending'];
        }

        $newStatus = match ($xenditStatus) {
            'PAID', 'SETTLED' => 'success',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            default => 'pending',
        };

        if ($newStatus === 'pending') {
            return ['status' => 'pending'];
        }

        try {
            return DB::transaction(function () use ($xenditId, $newStatus, $xenditInvoice) {
                $lockedTransaction = Transaction::withoutGlobalScopes()
                    ->where('xendit_id', $xenditId)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedTransaction || $lockedTransaction->status !== 'pending') {
                    return [
                        'status' => $lockedTransaction?->status ?? 'not_found',
                        'payment_method' => $lockedTransaction?->payment_channel,
                    ];
                }

                $lockedTransaction->update([
                    'status' => $newStatus,
                    'xendit_response' => $xenditInvoice,
                ]);

                if ($newStatus === 'success') {
                    if ($lockedTransaction->type === 'sale') {
                        $sale = Sale::find($lockedTransaction->transactionable_id);
                        if ($sale) {
                            $sale->update(['payment_status' => 'paid']);
                        }
                    } elseif ($lockedTransaction->type === 'order') {
                        $order = Order::find($lockedTransaction->transactionable_id);
                        if ($order && ! in_array($order->payment_status, ['paid', 'success'])) {
                            $order->update(['payment_status' => 'success']);
                            try {
                                $order->transitionStatus('processing');
                            } catch (\InvalidArgumentException) {
                            }
                            $order->load('items');
                            $this->createSaleFromOrder($order);
                        }
                    }
                } elseif (in_array($newStatus, ['failed', 'expired']) && $lockedTransaction->type === 'order') {
                    $order = Order::find($lockedTransaction->transactionable_id);
                    if ($order && $order->payment_status === 'pending') {
                        $order->update(['payment_status' => $newStatus]);
                        try {
                            $order->transitionStatus('cancelled');
                        } catch (\InvalidArgumentException) {
                        }
                    }
                }

                $paymentChannel = $lockedTransaction->payment_channel;
                if (! $paymentChannel && $lockedTransaction->type === 'order') {
                    $order = Order::find($lockedTransaction->transactionable_id);
                    $paymentChannel = $order?->payment_method;
                }

                return [
                    'status' => $newStatus,
                    'payment_method' => $paymentChannel,
                ];
            });
        } catch (\Exception $e) {
            return ['status' => 'pending'];
        }
    }
}
