<?php

use App\Models\Category;


beforeEach(function () {
    seedRoles();
});

test('guest redirected to login for category index', function () {
    $response = $this->get(route('categories.index'));
    $response->assertRedirect('/login');
});

test('produksi can view category index', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->get(route('categories.index'));
    $response->assertStatus(200);
});

test('produksi can create category', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'New Category',
    ]);

    $response->assertStatus(302);
    expect(Category::where('name', 'New Category')->exists())->toBeTrue();
});

test('create category validates name', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->post(route('categories.store'), []);

    $response->assertSessionHasErrors(['name']);
});

test('produksi can update category', function () {
    $user = createUserWithRole('produksi');
    $category = Category::factory()->create(['name' => 'Old']);

    $response = $this->actingAs($user)->put(route('categories.update', $category), [
        'name' => 'Updated',
    ]);

    $response->assertStatus(302);
    expect($category->fresh()->name)->toBe('Updated');
});

test('produksi can delete empty category', function () {
    $user = createUserWithRole('produksi');
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

    $response->assertStatus(302);
    expect(Category::find($category->id))->toBeNull();
});

test('gudang can view categories', function () {
    $user = createUserWithRole('gudang');

    $response = $this->actingAs($user)->get(route('categories.index'));
    $response->assertStatus(200);
});

test('kasir can view categories', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get(route('categories.index'));
    $response->assertStatus(200);
});
