<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        seedRoles();
    }

    #[Test]
    public function guest_redirected_to_login_for_voucher_index()
    {
        $response = $this->get(route('vouchers.index'));
        $response->assertRedirect('/login');
    }

    #[Test]
    public function admin_can_view_voucher_index()
    {
        $user = createUserWithRole('admin');

        $response = $this->actingAs($user)->get(route('vouchers.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function kasir_cannot_view_voucher_index()
    {
        $user = createUserWithRole('kasir');

        $response = $this->actingAs($user)->get(route('vouchers.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_create_voucher()
    {
        $user = createUserWithRole('admin');

        $response = $this->actingAs($user)->post(route('vouchers.store'), [
            'code' => 'TEST-001',
            'type' => 'percentage',
            'value' => 15,
            'min_order' => 20000,
            'max_discount' => 15000,
        ]);

        $response->assertRedirect();
        expect(Voucher::where('code', 'TEST-001')->exists())->toBeTrue();
    }

    #[Test]
    public function create_voucher_validates_required_fields()
    {
        $user = createUserWithRole('admin');

        $response = $this->actingAs($user)->post(route('vouchers.store'), []);

        $response->assertSessionHasErrors(['code', 'type', 'value']);
    }

    #[Test]
    public function create_voucher_validates_unique_code()
    {
        $user = createUserWithRole('admin');
        Voucher::factory()->create(['code' => 'DUPLICATE']);

        $response = $this->actingAs($user)->post(route('vouchers.store'), [
            'code' => 'DUPLICATE',
            'type' => 'percentage',
            'value' => 10,
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    #[Test]
    public function admin_can_update_voucher()
    {
        $user = createUserWithRole('admin');
        $voucher = Voucher::factory()->create(['code' => 'ORIGINAL']);

        $response = $this->actingAs($user)->put(route('vouchers.update', $voucher), [
            'code' => 'UPDATED',
            'type' => 'nominal',
            'value' => 5000,
        ]);

        $response->assertRedirect();
        expect($voucher->fresh()->code)->toBe('UPDATED');
        expect($voucher->fresh()->type)->toBe('nominal');
    }

    #[Test]
    public function admin_can_deactivate_voucher()
    {
        $user = createUserWithRole('admin');
        $voucher = Voucher::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->delete(route('vouchers.destroy', $voucher));

        $response->assertRedirect();
        expect($voucher->fresh()->is_active)->toBeFalse();
    }

    #[Test]
    public function voucher_is_valid_for_sufficient_subtotal()
    {
        $voucher = Voucher::factory()->percentage(10)->create([
            'min_order' => 20000,
        ]);

        expect($voucher->isValidFor(30000))->toBeTrue();
        expect($voucher->isValidFor(10000))->toBeFalse();
    }

    #[Test]
    public function expired_voucher_is_not_valid()
    {
        $voucher = Voucher::factory()->expired()->create();

        expect($voucher->isValidFor(50000))->toBeFalse();
    }

    #[Test]
    public function inactive_voucher_is_not_valid()
    {
        $voucher = Voucher::factory()->inactive()->create();

        expect($voucher->isValidFor(50000))->toBeFalse();
    }

    #[Test]
    public function fully_used_voucher_is_not_valid()
    {
        $voucher = Voucher::factory()->limited(1)->create([
            'used_count' => 1,
        ]);

        expect($voucher->isValidFor(50000))->toBeFalse();
    }

    #[Test]
    public function percentage_discount_calculation()
    {
        $voucher = Voucher::factory()->percentage(10, 5000)->create();

        expect($voucher->calculateDiscount(30000))->toBe(3000.0);
        expect($voucher->calculateDiscount(100000))->toBe(5000.0);
    }

    #[Test]
    public function nominal_discount_calculation()
    {
        $voucher = Voucher::factory()->nominal(5000)->create();

        expect($voucher->calculateDiscount(30000))->toBe(5000.0);
        expect($voucher->calculateDiscount(3000))->toBe(3000.0);
    }

    #[Test]
    public function create_voucher_page_loads_for_admin()
    {
        $user = createUserWithRole('admin');

        $response = $this->actingAs($user)->get(route('vouchers.create'));

        $response->assertStatus(200);
    }

    #[Test]
    public function edit_voucher_page_loads_for_admin()
    {
        $user = createUserWithRole('admin');
        $voucher = Voucher::factory()->create();

        $response = $this->actingAs($user)->get(route('vouchers.edit', $voucher));

        $response->assertStatus(200);
    }
}
