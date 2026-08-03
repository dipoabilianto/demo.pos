<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('login with email works', function () {
    $user = User::where('email', 'kasir@oribun.app')->first();

    $response = $this->post('/login', [
        'login' => 'kasir@oribun.app',
        'password' => 'kasir123',
    ]);

    $response->assertRedirect('/login/captcha');
    $this->assertGuest();
});

test('login with name works (uppercase)', function () {
    $response = $this->post('/login', [
        'login' => 'kasir',
        'password' => 'kasir123',
    ]);

    $response->assertSessionHas('captcha:user_id');
});

test('login with name is case insensitive', function () {
    $response1 = $this->post('/login', [
        'login' => 'KASIR',
        'password' => 'kasir123',
    ]);

    $response2 = $this->post('/login', [
        'login' => 'Kasir',
        'password' => 'kasir123',
    ]);

    $response1->assertSessionHas('captcha:user_id');
    $response2->assertSessionHas('captcha:user_id');
});

test('name is stored uppercase', function () {
    $user = User::create([
        'name' => 'test name',
        'email' => 'testname@test.com',
        'password' => bcrypt('test123'),
        'role' => 'kasir',
    ]);

    expect($user->name)->toBe('TEST NAME');

    $user->delete();
});

test('superadmin has all permissions', function () {
    $superadmin = User::where('email', 'superadmin@oribun.app')->first();

    expect($superadmin->isSuperadmin())->toBeTrue();
    expect($superadmin->hasPermission('products.view'))->toBeTrue();
    expect($superadmin->hasPermission('security.manage'))->toBeTrue();
    expect($superadmin->hasPermission('payment-methods.view'))->toBeTrue();

    $effective = $superadmin->getEffectivePermissions();
    $expectedCount = collect(config('permissions.modules'))->sum(fn($m) => count($m['permissions']));
    expect(count($effective))->toBe($expectedCount);
});

test('admin has all non-superadmin permissions', function () {
    $admin = User::where('email', 'admin@oribun.app')->first();

    expect($admin->hasPermission('products.view'))->toBeTrue();
    expect($admin->hasPermission('vouchers.view'))->toBeTrue();
    expect($admin->hasPermission('users.view'))->toBeTrue();
    expect($admin->hasPermission('settings.view'))->toBeTrue();
    expect($admin->hasPermission('security.manage'))->toBeFalse();
    expect($admin->hasPermission('payment-methods.view'))->toBeTrue();
});

test('kasir has sales and orders permissions', function () {
    $kasir = User::where('email', 'kasir@oribun.app')->first();

    expect($kasir->hasPermission('sales.view'))->toBeTrue();
    expect($kasir->hasPermission('sales.create'))->toBeTrue();
    expect($kasir->hasPermission('orders.view'))->toBeTrue();
    expect($kasir->hasPermission('orders.create'))->toBeTrue();
    expect($kasir->hasPermission('payments.process'))->toBeTrue();
    expect($kasir->hasPermission('products.view'))->toBeFalse();
    expect($kasir->hasPermission('expenses.view'))->toBeFalse();
    expect($kasir->hasPermission('vouchers.view'))->toBeFalse();
});

test('produksi has products permissions', function () {
    $produksi = User::where('email', 'produksi@oribun.app')->first();

    expect($produksi->hasPermission('products.view'))->toBeTrue();
    expect($produksi->hasPermission('products.create'))->toBeTrue();
    expect($produksi->hasPermission('categories.view'))->toBeTrue();
    expect($produksi->hasPermission('sales.view'))->toBeFalse();
    expect($produksi->hasPermission('expenses.view'))->toBeFalse();
});

test('gudang has expenses and stock permissions', function () {
    $gudang = User::where('email', 'gudang@oribun.app')->first();

    expect($gudang->hasPermission('expenses.view'))->toBeTrue();
    expect($gudang->hasPermission('expenses.create'))->toBeTrue();
    expect($gudang->hasPermission('raw-materials.view'))->toBeTrue();
    expect($gudang->hasPermission('stock-opname.view'))->toBeTrue();
    expect($gudang->hasPermission('products.view'))->toBeTrue();
    expect($gudang->hasPermission('sales.view'))->toBeFalse();
    expect($gudang->hasPermission('orders.view'))->toBeFalse();
});

test('multi-role user (kasir+gudang) has combined permissions', function () {
    $multi = User::where('email', 'kasirgudang@oribun.app')->first();

    expect($multi->roles->pluck('name')->toArray())->toBe(['kasir', 'gudang']);

    expect($multi->hasPermission('sales.view'))->toBeTrue();
    expect($multi->hasPermission('orders.view'))->toBeTrue();
    expect($multi->hasPermission('expenses.view'))->toBeTrue();
    expect($multi->hasPermission('raw-materials.view'))->toBeTrue();
    expect($multi->hasPermission('products.view'))->toBeTrue();
    expect($multi->hasPermission('vouchers.view'))->toBeFalse();
});

test('multi-role user (kasir+produksi) has combined permissions', function () {
    $multi = User::where('email', 'kasirproduksi@oribun.app')->first();

    expect($multi->roles->pluck('name')->toArray())->toBe(['kasir', 'produksi']);

    expect($multi->hasPermission('sales.view'))->toBeTrue();
    expect($multi->hasPermission('orders.view'))->toBeTrue();
    expect($multi->hasPermission('products.view'))->toBeTrue();
    expect($multi->hasPermission('products.create'))->toBeTrue();
    expect($multi->hasPermission('categories.view'))->toBeTrue();
    expect($multi->hasPermission('expenses.view'))->toBeFalse();
});

test('user with permission override can have custom permissions', function () {
    $user = User::create([
        'name' => 'Custom Permission',
        'email' => 'custom@test.com',
        'password' => bcrypt('test123'),
        'role' => 'kasir',
        'permissions' => ['products.view', 'vouchers.view'],
    ]);
    $user->roles()->attach(Role::where('name', 'kasir')->first());

    $freshUser = User::find($user->id);

    // Should have kasir defaults + overrides
    expect($freshUser->hasPermission('sales.view'))->toBeTrue();
    expect($freshUser->hasPermission('products.view'))->toBeTrue();
    expect($freshUser->hasPermission('vouchers.view'))->toBeTrue();
    expect($freshUser->hasPermission('expenses.view'))->toBeFalse();

    $user->delete();
});

test('dashboard route redirects to login for guests', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

test('products route is protected by permission', function () {
    $kasir = User::where('email', 'kasir@oribun.app')->first();

    $response = $this->actingAs($kasir)->get('/products');
    $response->assertStatus(403);

    $produksi = User::where('email', 'produksi@oribun.app')->first();
    $response = $this->actingAs($produksi)->get('/products');
    $response->assertStatus(200);
});

test('expenses route is protected by permission', function () {
    $kasir = User::where('email', 'kasir@oribun.app')->first();

    $response = $this->actingAs($kasir)->get('/expenses');
    $response->assertStatus(403);

    $gudang = User::where('email', 'gudang@oribun.app')->first();
    $response = $this->actingAs($gudang)->get('/expenses');
    $response->assertStatus(200);
});

test('sales route is protected by permission', function () {
    $produksi = User::where('email', 'produksi@oribun.app')->first();

    $response = $this->actingAs($produksi)->get('/sales');
    $response->assertStatus(403);

    $kasir = User::where('email', 'kasir@oribun.app')->first();
    $response = $this->actingAs($kasir)->get('/sales');
    $response->assertStatus(200);
});

test('vouchers route is protected by permission', function () {
    $kasir = User::where('email', 'kasir@oribun.app')->first();

    $response = $this->actingAs($kasir)->get('/vouchers');
    $response->assertStatus(403);

    $admin = User::where('email', 'admin@oribun.app')->first();
    $response = $this->actingAs($admin)->get('/vouchers');
    $response->assertStatus(200);
});

test('settings route is protected by permission', function () {
    $kasir = User::where('email', 'kasir@oribun.app')->first();

    $response = $this->actingAs($kasir)->get('/settings');
    $response->assertStatus(403);

    $admin = User::where('email', 'admin@oribun.app')->first();
    $response = $this->actingAs($admin)->get('/settings');
    $response->assertStatus(200);
});

test('security route is superadmin only', function () {
    $admin = User::where('email', 'admin@oribun.app')->first();

    $response = $this->actingAs($admin)->get('/settings/security/2fa/setup');
    $response->assertStatus(403);

    $superadmin = User::where('email', 'superadmin@oribun.app')->first();
    $response = $this->actingAs($superadmin)->get('/settings/payment-methods');
    $response->assertStatus(200);
});

test('multi-role user can access combined routes', function () {
    $kasirGudang = User::where('email', 'kasirgudang@oribun.app')->first();

    $response = $this->actingAs($kasirGudang)->get('/sales');
    $response->assertStatus(200);

    $response = $this->actingAs($kasirGudang)->get('/expenses');
    $response->assertStatus(200);

    $response = $this->actingAs($kasirGudang)->get('/products');
    $response->assertStatus(200);
});
