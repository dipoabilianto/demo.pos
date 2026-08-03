<?php

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    seedRoles();
});

test('guest redirected to login for sale index', function () {
    $response = $this->get(route('sales.index'));
    $response->assertRedirect('/login');
});

test('kasir can view sale index', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get(route('sales.index'));

    $response->assertStatus(200);
});

test('produksi cannot view sale index', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->get(route('sales.index'));

    $response->assertStatus(403);
});

test('kasir can create cash sale', function () {
    $user = createUserWithRole('kasir');
    $product = Product::factory()->create([
        'price' => 15000,
        'stock' => 50,
        'is_unlimited' => false,
    ]);
    Branch::factory()->create();

    $response = $this->actingAs($user)->post(route('sales.store'), [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
        'payment_method' => 'cash',
        'paid_amount' => 50000,
    ]);

    $response->assertSessionHas('success');
    $product->refresh();
    expect((int) $product->stock)->toBe(48);
});

test('sale creation validates items', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->post(route('sales.store'), [
        'items' => [],
        'payment_method' => 'cash',
    ]);

    $response->assertSessionHasErrors();
});

test('kasir can view sale detail', function () {
    $user = createUserWithRole('kasir');
    $sale = Sale::factory()->create();

    $response = $this->actingAs($user)->get(route('sales.show', $sale));

    $response->assertStatus(200);
});

test('create sale redirects to order catalog', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get(route('sales.create'));

    $response->assertRedirect(route('orders.catalog'));
});
