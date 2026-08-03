<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingService
{
    private static ?array $cachedSettings = null;

    private const DEFAULTS = [
        'store_name' => 'Oribun Bakery',
        'store_address' => '',
        'store_phone' => '',
        'store_email' => '',
        'store_whatsapp' => '',
        'store_description' => 'Toko roti homemade dengan bahan-bahan berkualitas terbaik.',
        'currency' => 'IDR',
        'notification_email' => '',
        'notification_whatsapp' => '',
        'order_confirmation_email' => true,
        'order_status_email' => true,
        'low_stock_notification' => true,
        'notification_sound_enabled' => true,
        'notification_sound_preset' => 'nada1',
        'notification_sound_file' => null,
        'receipt_logo' => null,
        'receipt_footer_note' => 'Terima kasih telah berbelanja di Oribun Bakery.',
        'receipt_kitchen_note' => '',
        'theme_primary' => '#d97706',
        'theme_sidebar' => '#3b1e10',
        'theme_sidebar_text' => '#ffffff',
        'theme_accent' => '#f59e0b',
        'receipt_show_header' => true,
        'receipt_show_prices' => true,
        'printer_model' => '',
        'printer_paper_size' => '58',
        'receipt_show_cash_change' => true,
        'store_hours' => '08:00 - 21:00',
        'store_instagram' => '',
        'login_logo' => null,
        'login_description' => '',
        'login_background' => null,
        'promotions' => [],
        'tax_enabled' => false,
        'tax_name' => 'PPN',
        'tax_rate' => 11,
        'tax_type' => 'exclude',
        'attendance_radius_mode' => 'warning',
        'xendit_secret_key' => '',
        'xendit_public_key' => '',
        'xendit_webhook_secret' => '',
        'qris_manual_image' => null,
    ];

    public function getSettings(?int $branchId = null): array
    {
        $global = Cache::remember('app_settings', 300, function () {
            $rows = Setting::whereNull('branch_id')->get(['key', 'value']);
            if ($rows->isNotEmpty()) {
                $settings = [];
                foreach ($rows as $row) {
                    $settings[$row->key] = $row->value !== null ? (json_decode($row->value, true) ?? $row->value) : null;
                }
                return array_merge(self::DEFAULTS, $settings);
            }

            return self::DEFAULTS;
        });

        if ($branchId && isset($global['promotions'])) {
            $global['promotions'] = array_values(array_filter($global['promotions'], function ($p) use ($branchId) {
                $branchIds = $p['branch_ids'] ?? [];

                return empty($branchIds) || in_array($branchId, $branchIds);
            }));
        }

        return $global;
    }

    public function saveSettings(array $settings): void
    {
        DB::transaction(function () use ($settings) {
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key, 'branch_id' => null],
                    ['value' => is_array($value) ? json_encode($value) : (is_bool($value) ? ($value ? '1' : '0') : $value)]
                );
            }
        });
        Cache::forget('app_settings');
    }

    public function getGeneralData(Request $request): array
    {
        $settings = $this->getSettings();
        $tab = $request->tab ?? 'general';
        $user = auth()->user();

        $data = compact('settings', 'tab');
        $data['dayNames'] = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        $data['roleColors'] = [
            'superadmin' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'ring' => 'ring-rose-200', 'dot' => 'bg-rose-400'],
            'admin' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'ring' => 'ring-purple-200', 'dot' => 'bg-purple-400'],
            'kasir' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200', 'dot' => 'bg-emerald-400'],
            'produksi' => ['bg' => 'bg-theme-primary/10', 'text' => 'text-theme-primary', 'ring' => 'ring-theme-primary/20', 'dot' => 'bg-theme-primary'],
            'gudang' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700', 'ring' => 'ring-sky-200', 'dot' => 'bg-sky-400'],
        ];

        if ($user->hasPermission('users.view') || $user->hasPermission('roles.view')) {
            $data['permissionModules'] = config('permissions.modules');
            $data['allRoles'] = Role::all();
        }

        if ($user->hasPermission('users.view')) {
            $query = User::with(['roles', 'branch'])->where('role', '!=', 'superadmin');
            if ($search = $request->search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            }
            $data['users'] = $query->latest()->paginate(15);
        }

        if ($user->hasPermission('roles.view')) {
            $data['roles'] = Role::withCount('users')->orderBy('name')->get();
        }

        if ($user->hasPermission('users.view') || $user->hasPermission('shifts.view')) {
            $data['branches'] = Branch::with('shifts.schedules.user', 'users')->get();
        } else {
            $data['branches'] = collect();
        }

        return $data;
    }
}
