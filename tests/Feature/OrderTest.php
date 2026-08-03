<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;


beforeEach(function () {
    seedRoles();
});

test('guest redirected to login for order catalog', function () {
    $response = $this->get(route('orders.catalog'));
    $response->assertRedirect('/login');
});

test('authenticated user can view order catalog', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get(route('orders.catalog'));

    $response->assertStatus(200);
});

test('guest redirected to login for order history', function () {
    $response = $this->get(route('orders.history'));
    $response->assertRedirect('/login');
});

test('kasir can view order history', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get(route('orders.history'));

    $response->assertStatus(200);
});

test('produksi cannot view order history', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->get(route('orders.history'));

    $response->assertStatus(403);
});

test('superadmin can process order', function () {
    $user = createUserWithRole('superadmin', ['email' => 'superadmin@oribun.app']);
    $order = Order::factory()->create([
        'order_status' => 'pending',
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user)->post(route('orders.process', $order));

    $response->assertRedirect();
    expect($order->fresh()->order_status)->toBe('confirmed');
});

test('kasir can process their own order', function () {
    $user = createUserWithRole('kasir');
    $order = Order::factory()->create([
        'order_status' => 'pending',
        'payment_status' => 'paid',
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('orders.process', $order));

    $response->assertRedirect();
    expect($order->fresh()->order_status)->toBe('confirmed');
});

test('cannot process already processed order', function () {
    $user = createUserWithRole('kasir');
    $order = Order::factory()->create([
        'order_status' => 'pending',
        'payment_status' => 'paid',
        'user_id' => $user->id,
        'processed_by' => $user->id,
        'processed_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('orders.process', $order));

    $response->assertRedirect();
    expect($order->fresh()->order_status)->toBe('pending');
});

test('superadmin can complete order', function () {
    $user = createUserWithRole('superadmin', ['email' => 'superadmin@oribun.app']);
    $order = Order::factory()->create([
        'order_status' => 'confirmed',
        'payment_status' => 'paid',
        'processed_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('orders.complete', $order));

    $response->assertRedirect();
    expect($order->fresh()->order_status)->toBe('completed');
});

test('cannot complete order without processing first', function () {
    $user = createUserWithRole('kasir');
    $order = Order::factory()->create([
        'order_status' => 'pending',
        'payment_status' => 'paid',
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('orders.complete', $order));

    expect($order->fresh()->order_status)->toBe('pending');
});

test('superadmin can view any order', function () {
    $user = createUserWithRole('superadmin', ['email' => 'superadmin@oribun.app']);
    $order = Order::factory()->create();

    $response = $this->actingAs($user)->get(route('orders.show', $order));

    $response->assertStatus(200);
});

test('kasir can view their own order', function () {
    $user = createUserWithRole('kasir');
    $order = Order::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get(route('orders.show', $order));

    $response->assertStatus(200);
});

test('kasir can save a draft order', function () {
    $user = createUserWithRole('kasir');
    $product = Product::factory()->create(['price' => 25000, 'stock' => 100]);

    $response = $this->actingAs($user)->post(route('orders.save'), [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2, 'notes' => ''],
        ],
        'customer_name' => 'Test Customer',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

test('save validates items', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->post(route('orders.save'), [
        'items' => [],
    ], ['Accept' => 'application/json']);

    expect(in_array($response->status(), [422, 302]))->toBeTrue();
});

test('saved list returns saved orders', function () {
    $user = createUserWithRole('kasir');
    Order::factory()->count(3)->create([
        'user_id' => $user->id,
        'order_status' => 'pending',
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get(route('orders.saved-list'));

    $response->assertStatus(200);
});

test('public catalog shows online branch', function () {
    $branch = \App\Models\Branch::factory()->create([
        'is_online' => true,
        'is_active' => true,
    ]);

    $response = $this->get(route('orders.public-catalog', $branch));

    $response->assertStatus(200);
});

test('check voucher validates required fields', function () {
    $response = $this->get('/orders/public/check-voucher');

    expect(in_array($response->status(), [302, 404]))->toBeTrue();
});

test('guest cannot access save order', function () {
    $response = $this->post(route('orders.save'), []);
    $response->assertRedirect('/login');
});
