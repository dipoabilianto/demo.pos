<?php



beforeEach(function () {
    $this->seed();
});

test('search by name returns matching products', function () {
    $response = $this->getJson('/api/products/search?q=Espresso');

    $response->assertStatus(200);
    $data = $response->json();
    expect($data)->toBeArray();
    expect(count($data))->toBeGreaterThanOrEqual(1);
    expect(collect($data)->pluck('name'))->toContain('Espresso');
});

test('search by SKU returns matching product', function () {
    $response = $this->getJson('/api/products/search?q=CAP-003');

    $response->assertStatus(200);
    $data = $response->json();
    expect($data)->toBeArray();
    expect(count($data))->toBeGreaterThanOrEqual(1);
    expect($data[0]['sku'])->toBe('CAP-003');
});

test('search by partial name returns matches', function () {
    $response = $this->getJson('/api/products/search?q=latte');

    $response->assertStatus(200);
    $data = $response->json();
    expect($data)->toBeArray();
    $names = collect($data)->pluck('name')->map(fn($n) => strtolower($n))->toArray();
    expect($names)->each->toContain('latte');
});

test('empty query returns empty array', function () {
    $response = $this->getJson('/api/products/search?q=');
    $response->assertStatus(200);
    expect($response->json())->toBe([]);

    $response = $this->getJson('/api/products/search');
    $response->assertStatus(200);
    expect($response->json())->toBe([]);
});

test('no match returns empty array', function () {
    $response = $this->getJson('/api/products/search?q=zzzzznotfound');
    $response->assertStatus(200);
    expect($response->json())->toBe([]);
});

test('search excludes inactive products', function () {
    $product = \App\Models\Product::where('is_active', true)->first();
    $product->update(['is_active' => false]);

    $response = $this->getJson('/api/products/search?q=' . $product->name);
    $data = $response->json();
    $ids = collect($data)->pluck('id')->toArray();
    expect($ids)->not->toContain($product->id);
});
