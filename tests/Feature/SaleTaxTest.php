<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;


beforeEach(function () {
    $this->seed();
});

test('createSaleFromOrder preserves tax', function () {
    $product = Product::where('is_active', true)->first();

    $order = Order::create([
        'order_number' => 'ORDOF-TAX-0001',
        'customer_name' => 'Test Tax',
        'payment_method' => 'transfer',
        'payment_status' => 'paid',
        'order_status' => 'processing',
        'subtotal' => 50000,
        'discount' => 0,
        'tax' => 5000,
        'total' => 55000,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 50000,
        'quantity' => 1,
        'subtotal' => 50000,
    ]);

    $controller = app(\App\Http\Controllers\OrderController::class);
    $refMethod = new \ReflectionMethod($controller, 'createSaleFromOrder');
    $refMethod->setAccessible(true);
    $refMethod->invoke($controller, $order);

    $sale = Sale::where('invoice_number', 'like', 'INV-%')->latest()->first();
    expect($sale)->not->toBeNull();
    expect((float) $sale->tax)->toBe(5000.0);
    expect((float) $sale->total)->toBe(55000.0);
});

test('createSaleFromOrder handles zero tax', function () {
    $product = Product::where('is_active', true)->first();

    $order = Order::create([
        'order_number' => 'ORDOF-NOTAX-0001',
        'customer_name' => 'Test No Tax',
        'payment_method' => 'cash',
        'payment_status' => 'paid',
        'order_status' => 'processing',
        'subtotal' => 30000,
        'discount' => 0,
        'tax' => 0,
        'total' => 30000,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 30000,
        'quantity' => 1,
        'subtotal' => 30000,
    ]);

    $controller = app(\App\Http\Controllers\OrderController::class);
    $refMethod = new \ReflectionMethod($controller, 'createSaleFromOrder');
    $refMethod->setAccessible(true);
    $refMethod->invoke($controller, $order);

    $sale = Sale::where('invoice_number', 'like', 'INV-%')->latest()->first();
    expect($sale)->not->toBeNull();
    expect((float) $sale->tax)->toBe(0.0);
    expect((float) $sale->total)->toBe(30000.0);
});
