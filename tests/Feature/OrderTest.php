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
    $response->assertViewHas('branch', fn ($resolved) => $resolved->id === $branch->id);
});

test('public catalog URL is bound to the branch slug, not a guessable id', function () {
    // The route is /orders/public/{branch:slug} — resolution comes from Eloquent route
    // model binding on an unambiguous, unique column, not from parsing query strings
    // (the source of the branch-mixup bugs this route used to have).
    $branchA = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    $branchB = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);

    expect(route('orders.public-catalog', $branchB))->toContain($branchB->slug);

    $response = $this->get(route('orders.public-catalog', $branchB));
    $response->assertStatus(200);
    $response->assertViewHas('branch', fn ($resolved) => $resolved->id === $branchB->id);
});

test('public catalog 404s for an unknown branch slug instead of falling back to a different branch', function () {
    \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);

    $response = $this->get('/orders/public/this-slug-does-not-exist');

    $response->assertStatus(404);
});

test('public catalog 404s for a deactivated branch', function () {
    $branch = \App\Models\Branch::factory()->create(['is_active' => false, 'is_online' => false]);

    $response = $this->get(route('orders.public-catalog', $branch));

    $response->assertStatus(404);
});

test('public catalog still renders an active branch that is temporarily offline, instead of swapping to another branch', function () {
    $offlineBranch = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => false]);
    \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);

    $response = $this->get(route('orders.public-catalog', $offlineBranch));

    $response->assertStatus(200);
    $response->assertViewHas('branch', fn ($resolved) => $resolved->id === $offlineBranch->id);
    $response->assertViewHas('isOnline', false);
});

test('public catalog shows the branch\'s own products even when the viewer is staff logged in on a different branch', function () {
    // Regression: getPublicCatalogProducts() queried Product::with('category')... without
    // withoutGlobalScopes(), so ProductBranchScope ANDed in session('branch_id') on top of
    // the explicit $branch filter. A guest (no session branch) was unaffected, but a
    // logged-in kasir/admin browsing a DIFFERENT branch's storefront than the one in their
    // own session got zero products — reproduced live before fixing.
    $ownBranch = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    $otherBranch = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    Product::factory()->create(['branch_id' => $otherBranch->id, 'is_active' => true]);

    $user = createUserWithRole('kasir');
    session(['branch_id' => $ownBranch->id]);

    $response = $this->actingAs($user)->get(route('orders.public-catalog', $otherBranch));

    $response->assertStatus(200);
    $response->assertViewHas('products', fn ($products) => $products->flatten()->isNotEmpty());
});

test('bare /orders/public redirects to the canonical per-branch URL', function () {
    $branch = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);

    $response = $this->get(route('orders.public-catalog.default'));

    $response->assertRedirect(route('orders.public-catalog', $branch));
});

test('legacy ?branch_id= link redirects to that branch\'s canonical URL, not the default one', function () {
    \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    $intended = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);

    $response = $this->get(route('orders.public-catalog.default').'?branch_id='.$intended->id);

    $response->assertRedirect(route('orders.public-catalog', $intended));
});

test('a promotion missing optional fields does not crash the storefront', function () {
    // Regression: public-catalog.blade.php used to access $promo['description'] etc.
    // directly, so a promo entry saved without every key (partial form submit, manual
    // data edit) 500'd the entire branch storefront instead of degrading gracefully.
    $branch = \App\Models\Branch::factory()->create(['is_online' => true, 'is_active' => true]);
    \App\Models\Setting::create([
        'key' => 'promotions',
        'branch_id' => null,
        'value' => json_encode([['id' => 1, 'title' => 'Promo Tanpa Deskripsi', 'active' => true]]),
    ]);

    $response = $this->get(route('orders.public-catalog', $branch));

    $response->assertStatus(200);
    $response->assertSee('Promo Tanpa Deskripsi');
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
