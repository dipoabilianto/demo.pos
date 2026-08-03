<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;


beforeEach(function () {
    seedRoles();
});

test('login page loads', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('guest redirected to login for dashboard', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

test('login requires credentials', function () {
    $response = $this->post('/login', []);

    $response->assertSessionHasErrors(['login']);
});

test('login with invalid credentials returns errors', function () {
    $response = $this->post('/login', [
        'login' => 'nonexistent@test.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors();
});

test('superadmin login works without captcha', function () {
    $user = createSuperadmin(['email' => 'admin@oribun.app']);

    $response = $this->post('/login', [
        'login' => 'admin@oribun.app',
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
});

test('login redirects superadmin to dashboard', function () {
    $user = createSuperadmin(['email' => 'admin@oribun.app']);

    $response = $this->post('/login', [
        'login' => 'admin@oribun.app',
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
});

test('kasir redirects to captcha after login', function () {
    $user = createUserWithRole('kasir', ['email' => 'kasir@oribun.app']);

    $response = $this->post('/login', [
        'login' => 'kasir@oribun.app',
        'password' => 'password',
    ]);

    $response->assertRedirect('/login/captcha');
});

test('logout works', function () {
    $user = createSuperadmin(['email' => 'admin@oribun.app']);

    $this->actingAs($user);
    $response = $this->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
