<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    seedRoles();
});

test('guest redirected to login for product index', function () {
    $response = $this->get(route('products.index'));
    $response->assertRedirect('/login');
});

test('produksi can view product index', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->get(route('products.index'));
    $response->assertStatus(200);
});

test('kasir can view product index', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get(route('products.index'));
    $response->assertStatus(200);
});

test('produksi can create product', function () {
    $user = createUserWithRole('produksi');
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Test Product',
        'price' => 25000,
        'category_id' => $category->id,
        'stock' => 50,
    ]);

    $response->assertRedirect();
    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});

test('create product validates required fields', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->post(route('products.store'), []);

    $response->assertSessionHasErrors(['name', 'price']);
});

test('gudang cannot create product', function () {
    $user = createUserWithRole('gudang');

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Test',
        'price' => 10000,
    ]);

    $response->assertStatus(403);
});

test('produksi can update product', function () {
    $user = createUserWithRole('produksi');
    $product = Product::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'name' => 'Updated Name',
        'price' => 30000,
    ]);

    $response->assertRedirect();
    expect($product->fresh()->name)->toBe('Updated Name');
});

test('produksi can deactivate product', function () {
    $user = createUserWithRole('produksi');
    $product = Product::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)->delete(route('products.destroy', $product));

    $response->assertRedirect();
    expect($product->fresh()->is_active)->toBeFalse();
});

test('toggle sold out changes status', function () {
    $user = createUserWithRole('produksi');
    $product = Product::factory()->create(['is_sold_out' => false]);

    $response = $this->actingAs($user)->post(route('products.toggle-sold', $product));

    $response->assertStatus(200);
    expect($product->fresh()->is_sold_out)->toBeTrue();
});

test('product index filters by stock status', function () {
    $user = createUserWithRole('produksi');
    Product::factory()->lowStock()->create(['name' => 'Low Stock Item']);
    Product::factory()->create(['name' => 'Normal Stock', 'stock' => 100]);

    $response = $this->actingAs($user)->get(route('products.index', ['stock_status' => 'low']));

    $response->assertStatus(200);
});

test('create product page loads for produksi', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->get(route('products.create'));

    $response->assertStatus(200);
});
