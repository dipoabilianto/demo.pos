<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    seedRoles();

    Config::set('services.xendit.webhook_secret', 'test-webhook-secret');
    Config::set('services.xendit.secret_key', 'test-secret-key');
});

function buildWebhookPayload(string $externalId, string $status): array
{
    return [
        'id' => 'inv-fake-' . Str::random(8),
        'external_id' => $externalId,
        'status' => $status,
        'amount' => 50000,
        'payer_email' => 'customer@test.com',
        'payment_method' => 'BANK_TRANSFER',
        'payment_channel' => 'BCA',
        'paid_at' => now()->toIso8601String(),
    ];
}

function createPendingOrder(): Order
{
    $user = createUserWithRole('kasir');
    $product = Product::factory()->create([
        'stock' => 10,
        'is_unlimited' => false,
    ]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'order_status' => 'pending',
        'payment_status' => 'pending',
        'payment_method' => 'xendit',
        'total' => 50000,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 50000,
        'quantity' => 1,
        'subtotal' => 50000,
    ]);

    return $order;
}

function createTransaction(Order $order, string $externalId): Transaction
{
    return Transaction::create([
        'xendit_id' => 'inv-' . Str::random(8),
        'type' => 'order',
        'transactionable_type' => Order::class,
        'transactionable_id' => $order->id,
        'payment_channel' => 'BCA',
        'status' => 'pending',
        'amount' => $order->total,
        'external_id' => $externalId,
        'xendit_response' => [],
        'branch_id' => $order->branch_id,
    ]);
}

function xenditWebhook(array $payload): \Illuminate\Testing\TestResponse
{
    return test()->withServerVariables(['REMOTE_ADDR' => '52.76.126.98'])
        ->withHeader('x-callback-token', 'test-webhook-secret')
        ->postJson(route('payments.webhook.xendit'), $payload);
}

test('rejects non-Xendit IP with 403', function () {
    $response = test()->postJson(route('payments.webhook.xendit'), []);

    $response->assertStatus(403);
    $response->assertSee('Forbidden');
});

test('rejects missing callback token with 401', function () {
    $response = test()->withServerVariables(['REMOTE_ADDR' => '52.76.126.98'])
        ->postJson(route('payments.webhook.xendit'), []);

    $response->assertStatus(401);
    $response->assertSee('Missing callback token');
});

test('rejects wrong callback token with 401', function () {
    $response = test()->withServerVariables(['REMOTE_ADDR' => '52.76.126.98'])
        ->withHeader('x-callback-token', 'wrong-token')
        ->postJson(route('payments.webhook.xendit'), []);

    $response->assertStatus(401);
    $response->assertSee('Invalid signature');
});

test('processes PAID webhook creates sale and marks order processing', function () {
    $order = createPendingOrder();
    $externalId = 'ext-' . Str::random(10);
    $transaction = createTransaction($order, $externalId);

    $response = xenditWebhook(buildWebhookPayload($externalId, 'PAID'));

    $response->assertStatus(200);

    $order->refresh();
    expect($order->payment_status)->toBe('success');
    expect($order->order_status)->toBe('processing');

    $sale = \App\Models\Sale::where('payment_status', 'paid')->latest()->first();
    expect($sale)->not->toBeNull();
    expect((float) $sale->total)->toBe((float) $order->total);

    $transaction->refresh();
    expect($transaction->status)->toBe('success');
});

test('SETTLED webhook is idempotent to PAID', function () {
    $order = createPendingOrder();
    $externalId = 'ext-' . Str::random(10);
    createTransaction($order, $externalId);

    xenditWebhook(buildWebhookPayload($externalId, 'PAID'));
    $response = xenditWebhook(buildWebhookPayload($externalId, 'SETTLED'));

    $response->assertStatus(200);

    $saleCount = \App\Models\Sale::count();
    expect($saleCount)->toBe(1);
});

test('already processed transaction is idempotent', function () {
    $order = createPendingOrder();
    $externalId = 'ext-' . Str::random(10);
    $transaction = createTransaction($order, $externalId);

    $transaction->update(['status' => 'success']);

    $response = xenditWebhook(buildWebhookPayload($externalId, 'PAID'));

    $response->assertStatus(200);

    $order->refresh();
    expect($order->payment_status)->toBe('pending');
});

test('FAILED webhook cancels order', function () {
    $order = createPendingOrder();
    $externalId = 'ext-' . Str::random(10);
    createTransaction($order, $externalId);

    $response = xenditWebhook(buildWebhookPayload($externalId, 'FAILED'));

    $response->assertStatus(200);

    $order->refresh();
    expect($order->payment_status)->toBe('failed');
    expect($order->order_status)->toBe('cancelled');
});

test('EXPIRED webhook cancels order', function () {
    $order = createPendingOrder();
    $externalId = 'ext-' . Str::random(10);
    createTransaction($order, $externalId);

    $response = xenditWebhook(buildWebhookPayload($externalId, 'EXPIRED'));

    $response->assertStatus(200);

    $order->refresh();
    expect($order->payment_status)->toBe('expired');
    expect($order->order_status)->toBe('cancelled');
});
