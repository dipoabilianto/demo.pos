<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;


beforeEach(function () {
    $this->seed();
});

test('sale decrements product stock correctly', function () {
    $product = Product::create([
        'name' => 'Test Produk',
        'sku' => 'TST-001',
        'price' => 10000,
        'cost_price' => 5000,
        'stock' => 10,
        'is_unlimited' => false,
        'is_active' => true,
    ]);

    DB::table('products')->where('id', $product->id)->decrement('stock', 4);
    $product->refresh();

    expect((int) $product->stock)->toBe(6);
});

test('sale with insufficient stock fails via controller', function () {
    $product = Product::create([
        'name' => 'Test Produk',
        'sku' => 'TST-002',
        'price' => 10000,
        'cost_price' => 5000,
        'stock' => 3,
        'is_unlimited' => false,
        'is_active' => true,
    ]);

    $kasir = User::where('email', 'kasir@oribun.app')->first();

    $response = $this->actingAs($kasir)->post('/sales', [
        'items' => [['product_id' => $product->id, 'quantity' => 10]],
        'payment_method' => 'cash',
        'paid_amount' => 200000,
    ]);

    $response->assertSessionHasErrors();
    $product->refresh();
    expect((int) $product->stock)->toBe(3);
});
