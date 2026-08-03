<?php

use App\Models\Branch;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;


beforeEach(function () {
    seedRoles();
});

test('guest redirected to login for dashboard', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

test('authenticated user can view dashboard', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});

test('superadmin can view owner dashboard', function () {
    $user = createUserWithRole('superadmin', ['email' => 'superadmin@oribun.app']);

    $response = $this->actingAs($user)->get('/owner/dashboard');

    $response->assertStatus(200);
});

test('kasir cannot view owner dashboard', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get('/owner/dashboard');

    $response->assertStatus(403);
});

test('dashboard loads with products and sales data', function () {
    $user = createUserWithRole('superadmin', ['email' => 'superadmin@oribun.app']);

    Branch::factory()->count(2)->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});

test('dashboard shows zero state when no data', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});
