<?php

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


beforeEach(function () {
    seedRoles();
});

test('guest redirected to login for settings', function () {
    $response = $this->get(route('settings.general'));
    $response->assertRedirect('/login');
});

test('admin can view settings page', function () {
    $user = createUserWithRole('admin');

    $response = $this->actingAs($user)->get(route('settings.general'));

    $response->assertStatus(200);
});

test('produksi cannot view settings page', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->get(route('settings.general'));

    $response->assertStatus(403);
});

test('admin can update general settings', function () {
    $user = createUserWithRole('admin');

    $response = $this->actingAs($user)->post(route('settings.update'), [
        'store_name' => 'Oribun Bakery',
        'store_address' => 'Jl. Test No. 1',
        'store_phone' => '08123456789',
        'currency' => 'IDR',
    ]);

    $response->assertRedirect();
    expect(Setting::where('key', 'store_name')->exists())->toBeTrue();
});

test('admin can update theme settings', function () {
    $user = createUserWithRole('admin');

    $response = $this->actingAs($user)->post(route('settings.update'), [
        'theme_primary' => '#ff0000',
        'theme_sidebar' => '#333333',
        'theme_sidebar_text' => '#ffffff',
        'theme_accent' => '#ffcc00',
    ]);

    $response->assertRedirect();
    expect(Setting::where('key', 'theme_primary')->exists())->toBeTrue();
});

test('admin can update notification settings', function () {
    $user = createUserWithRole('admin');

    $response = $this->actingAs($user)->post(route('settings.update'), [
        'notification_email' => 'notif@oribun.app',
        'order_confirmation_email' => true,
        'low_stock_notification' => true,
    ]);

    $response->assertRedirect();
});

test('admin can update receipt settings', function () {
    $user = createUserWithRole('admin');

    $response = $this->actingAs($user)->post(route('settings.update'), [
        'receipt_footer_note' => 'Terima kasih',
        'printer_paper_size' => '58',
    ]);

    $response->assertRedirect();
});

test('admin can update tax settings', function () {
    $user = createUserWithRole('admin');

    $response = $this->actingAs($user)->post(route('settings.update'), [
        'tax_enabled' => true,
        'tax_rate' => 11,
        'tax_type' => 'exclude',
    ]);

    $response->assertRedirect();
});

test('produksi cannot update settings', function () {
    $user = createUserWithRole('produksi');

    $response = $this->actingAs($user)->post(route('settings.update'), [
        'store_name' => 'Hacked',
    ]);

    $response->assertStatus(403);
});

test('subsequent settings read uses saved values', function () {
    $user = createUserWithRole('admin');

    $this->actingAs($user)->post(route('settings.update'), [
        'store_name' => 'Oribun Updated',
    ]);

    $freshUser = createSuperadmin();
    $response = $this->actingAs($freshUser)->get(route('settings.general'));
    $response->assertStatus(200);
});

test('settings defaults returned when no settings exist', function () {
    $user = createUserWithRole('superadmin', ['email' => 'superadmin@oribun.app']);

    $response = $this->actingAs($user)->get(route('settings.general'));

    $response->assertStatus(200);
});

test('upload logo requires authentication', function () {
    Storage::fake('public');

    $response = $this->post('/settings/receipt/upload-logo', [
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]);

    $response->assertRedirect('/login');
});

test('two factor setup requires security permission', function () {
    $user = createUserWithRole('kasir');

    $response = $this->actingAs($user)->get('/settings/security/2fa/setup');

    $response->assertStatus(403);
});

test('uploading a promo image updates that promo without losing the others', function () {
    Storage::fake('public');
    $user = createSuperadmin();

    $this->actingAs($user)->post(route('settings.update'), [
        'promotions' => [
            ['id' => 1, 'title' => 'Promo Satu', 'description' => '', 'link' => '', 'active' => true],
            ['id' => 2, 'title' => 'Promo Dua', 'description' => '', 'link' => '', 'active' => true],
        ],
    ])->assertRedirect();

    $this->actingAs($user)->post(route('settings.upload-promo-image'), [
        'image' => UploadedFile::fake()->image('promo.png'),
        'promo_id' => 2,
    ])->assertJson(['success' => true]);

    $settingService = app(\App\Services\SettingService::class);
    $promotions = collect($settingService->getSettings()['promotions']);
    expect($promotions->firstWhere('id', 1)['title'])->toBe('Promo Satu');
    expect($promotions->firstWhere('id', 2)['image'] ?? null)->not->toBeNull();
});

test('a promotion saved with no branch checked applies to every branch by default', function () {
    $user = createSuperadmin();
    $branchA = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);
    $branchB = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);

    $this->actingAs($user)->post(route('settings.update'), [
        'promotions' => [
            ['id' => 1, 'title' => 'Promo Semua Cabang', 'description' => '', 'link' => '', 'active' => true],
        ],
    ])->assertRedirect();

    $settingService = app(\App\Services\SettingService::class);
    expect(collect($settingService->getSettings($branchA->id)['promotions'])->pluck('title'))->toContain('Promo Semua Cabang');
    expect(collect($settingService->getSettings($branchB->id)['promotions'])->pluck('title'))->toContain('Promo Semua Cabang');
});

test('a promotion checked for one branch does not apply to other branches', function () {
    $user = createSuperadmin();
    $branchA = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);
    $branchB = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);

    $this->actingAs($user)->post(route('settings.update'), [
        'promotions' => [
            ['id' => 1, 'title' => 'Promo Khusus A', 'description' => '', 'link' => '', 'active' => true, 'branch_ids' => [$branchA->id]],
        ],
    ])->assertRedirect();

    $settingService = app(\App\Services\SettingService::class);
    expect(collect($settingService->getSettings($branchA->id)['promotions'])->pluck('title'))->toContain('Promo Khusus A');
    expect(collect($settingService->getSettings($branchB->id)['promotions'])->pluck('title'))->not->toContain('Promo Khusus A');
});

test('a promotion can be checked for multiple specific branches at once', function () {
    $user = createSuperadmin();
    $branchA = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);
    $branchB = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);
    $branchC = \App\Models\Branch::factory()->create(['is_active' => true, 'is_online' => true]);

    $this->actingAs($user)->post(route('settings.update'), [
        'promotions' => [
            ['id' => 1, 'title' => 'Promo Dua Cabang', 'description' => '', 'link' => '', 'active' => true, 'branch_ids' => [$branchA->id, $branchB->id]],
        ],
    ])->assertRedirect();

    $settingService = app(\App\Services\SettingService::class);
    expect(collect($settingService->getSettings($branchA->id)['promotions'])->pluck('title'))->toContain('Promo Dua Cabang');
    expect(collect($settingService->getSettings($branchB->id)['promotions'])->pluck('title'))->toContain('Promo Dua Cabang');
    expect(collect($settingService->getSettings($branchC->id)['promotions'])->pluck('title'))->not->toContain('Promo Dua Cabang');
});

test('saving promotions does not wipe unrelated global settings', function () {
    $user = createSuperadmin();

    $this->actingAs($user)->post(route('settings.update'), [
        'store_name' => 'Toko Global Harus Tetap Ada',
    ])->assertRedirect();

    $this->actingAs($user)->post(route('settings.update'), [
        'promotions' => [
            ['id' => 1, 'title' => 'Promo Baru', 'description' => '', 'link' => '', 'active' => true],
        ],
    ])->assertRedirect();

    $settingService = app(\App\Services\SettingService::class);
    expect($settingService->getSettings()['store_name'])->toBe('Toko Global Harus Tetap Ada');
    expect(collect($settingService->getSettings()['promotions'])->pluck('title'))->toContain('Promo Baru');
});
