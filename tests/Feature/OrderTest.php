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

    $response = $this->get(route('orders.public-catalog', ['branch_id' => $branch->id]));

    $response->assertStatus(200);
});

test('public catalog resolves the requested branch, not just any online branch', function () {
    $branchA = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    $branchB = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);

    $response = $this->get(route('orders.public-catalog', ['branch_id' => $branchB->id]));

    $response->assertStatus(200);
    $response->assertViewHas('branch', fn ($branch) => $branch->id === $branchB->id);
});

test('public catalog degrades gracefully on a malformed key-less branch query', function () {
    // route('orders.public-catalog', $branch) with no {branch} URI segment produces a
    // key-less query string like ?6 instead of ?branch_id=6 — this is what
    // resources/views/components/layout/topbar.blade.php generated before it was fixed
    // to pass ['branch_id' => $branch->id] explicitly. The controller no longer tries to
    // guess a branch out of an arbitrary query key; it must fall back to the default
    // online branch instead of crashing or resolving an unrelated branch by accident.
    $branch = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);

    $malformedUrl = route('orders.public-catalog', $branch);
    expect($malformedUrl)->toContain('?'.$branch->id);

    $response = $this->get($malformedUrl);
    $response->assertStatus(200);
    $response->assertViewHas('branch', fn ($resolved) => $resolved->is_online && $resolved->is_active);
});

test('public product batch API does not leak another branch products when branch_id is given', function () {
    $branchA = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    $branchB = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    $productA = Product::factory()->create(['branch_id' => $branchA->id, 'is_active' => true]);
    $productB = Product::factory()->create(['branch_id' => $branchB->id, 'is_active' => true]);

    $response = $this->getJson('/api/products/batch?ids='.$productA->id.','.$productB->id.'&branch_id='.$branchA->id);

    $response->assertStatus(200);
    $ids = collect($response->json())->pluck('id');
    expect($ids)->toContain($productA->id);
    expect($ids)->not->toContain($productB->id);
});

test('check voucher validates required fields', function () {
    $response = $this->get('/orders/public/check-voucher');

    expect(in_array($response->status(), [302, 404]))->toBeTrue();
});

test('guest cannot access save order', function () {
    $response = $this->post(route('orders.save'), []);
    $response->assertRedirect('/login');
});
