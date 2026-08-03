<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Google2FAQRCode\Google2FA;

class SettingsController extends Controller
{
    public function __construct(
        private SettingService $settingService,
    ) {}
    public function general(Request $request)
    {
        $settings = $this->getSettings();
        $tab = $request->tab ?? 'general';
        $branches = \App\Models\Branch::active()->orderBy('name')->get(['id', 'name']);
        $paymentMethods = \App\Models\PaymentMethod::orderBy('group')->orderBy('name')->get();

        return view('settings.general', compact('settings', 'tab', 'branches', 'paymentMethods'));
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,gif|max:1024',
        ]);

        $path = $request->file('logo')->store('logos', 'public');
        $settings = $this->getSettings();
        $settings['receipt_logo'] = $path;
        $this->settingService->saveSettings($settings);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'path' => $path]);
        }

        return back()->with('success', 'Logo struk berhasil diunggah.');
    }

    public function uploadPromoImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'promo_id' => 'required|integer',
        ]);

        $path = $request->file('image')->store('promotions', 'public');
        $settings = $this->getSettings();

        $promotions = $settings['promotions'] ?? [];
        foreach ($promotions as &$promo) {
            if (($promo['id'] ?? null) == $request->promo_id) {
                $promo['image'] = $path;
                break;
            }
        }
        unset($promo);

        $this->settingService->saveSettings(['promotions' => $promotions]);

        return response()->json(['success' => true, 'path' => $path]);
    }

    public function uploadFavicon(Request $request): JsonResponse
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,jpg,jpeg,ico|max:512',
        ]);

        $path = $request->file('favicon')->store('favicons', 'public');
        $settings = $this->getSettings();
        $settings['favicon'] = $path;
        $this->settingService->saveSettings($settings);

        return response()->json(['success' => true, 'path' => asset('storage/' . $path)]);
    }

    public function uploadNotificationSound(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:mp3,wav,ogg|max:2048',
        ]);

        $path = $request->file('file')->store('notification-sounds', 'public');
        $settings = $this->getSettings();
        $settings['notification_sound_file'] = $path;
        $settings['notification_sound_preset'] = 'custom';
        $this->settingService->saveSettings($settings);

        return response()->json([
            'success' => true,
            'path' => asset('storage/' . $path),
            'filename' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    public function uploadQRIS(Request $request): JsonResponse
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:1024',
        ]);

        $path = $request->file('qris_image')->store('qris', 'public');
        $settings = $this->getSettings();
        $settings['qris_manual_image'] = $path;
        $this->settingService->saveSettings($settings);

        return response()->json(['success' => true, 'path' => asset('storage/' . $path)]);
    }

    public function removeQRIS(): JsonResponse
    {
        $settings = $this->getSettings();
        $settings['qris_manual_image'] = null;
        $this->settingService->saveSettings($settings);

        return response()->json(['success' => true]);
    }

    public function update(Request $request)
    {
        $rules = [
            'store_name' => 'nullable|string|max:255',
            'store_address' => 'nullable|string',
            'store_phone' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:255',
            'store_whatsapp' => 'nullable|string|max:20',
            'store_description' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'catalog_title' => 'nullable|string|max:100',
            'bg_base' => 'nullable|string|max:7',
            'bg_gradient' => 'nullable|string|max:7',
            'bg_blob' => 'nullable|string|max:7',
            'theme_primary' => 'nullable|string|max:7',
            'theme_sidebar' => 'nullable|string|max:7',
            'theme_sidebar_text' => 'nullable|string|max:7',
            'theme_accent' => 'nullable|string|max:7',
            'notification_email' => 'nullable|email|max:255',
            'notification_whatsapp' => 'nullable|string|max:20',
            'order_confirmation_email' => 'nullable|boolean',
            'order_status_email' => 'nullable|boolean',
            'low_stock_notification' => 'nullable|boolean',
            'notification_sound_enabled' => 'nullable|boolean',
            'notification_sound_preset' => 'nullable|string|in:nada1,nada2,nada3,nada4,custom',
            'receipt_footer_note' => 'nullable|string',
            'receipt_kitchen_note' => 'nullable|string',
            'receipt_show_prices' => 'nullable|boolean',
            'printer_model' => 'nullable|string|max:50',
            'printer_paper_size' => 'nullable|string|in:58,80',
            'store_hours' => 'nullable|string|max:50',
            'store_instagram' => 'nullable|string|max:100',
            'receipt_show_cash_change' => 'nullable|boolean',
            'xendit_secret_key' => 'nullable|string',
            'xendit_public_key' => 'nullable|string',
            'xendit_webhook_secret' => 'nullable|string',
            'promotions' => 'nullable|array',
            'promotions.*.id' => 'nullable|integer',
            'promotions.*.title' => 'nullable|string|max:255',
            'promotions.*.description' => 'nullable|string',
            'promotions.*.link' => 'nullable|url|max:255',
            'promotions.*.image' => 'nullable|string|max:255',
            'promotions.*.active' => 'nullable|boolean',
            'promotions.*.branch_ids' => 'nullable|array',
            'promotions.*.branch_ids.*' => 'integer|exists:branches,id',
            'tax_enabled' => 'nullable|boolean',
            'tax_name' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_type' => 'nullable|string|in:exclude,include',
        ];

        $user = auth()->user();
        $allowedFields = ['store_name', 'store_address', 'store_phone', 'store_email', 'store_whatsapp', 'store_description', 'currency', 'catalog_title', 'bg_base', 'bg_gradient', 'bg_blob'];
        if ($user?->hasPermission('settings.tax')) {
            $allowedFields = array_merge($allowedFields, ['tax_enabled', 'tax_name', 'tax_rate', 'tax_type']);
        }
        if ($user?->hasPermission('settings.notifications')) {
            $allowedFields = array_merge($allowedFields, ['notification_email', 'notification_whatsapp', 'order_confirmation_email', 'order_status_email', 'low_stock_notification', 'notification_sound_enabled', 'notification_sound_preset']);
        }
        if ($user?->hasPermission('settings.receipt')) {
            $allowedFields = array_merge($allowedFields, ['receipt_footer_note', 'receipt_kitchen_note', 'receipt_show_prices', 'printer_model', 'printer_paper_size', 'store_hours', 'store_instagram', 'receipt_show_cash_change']);
        }
        if ($user?->hasPermission('settings.promotions')) {
            $allowedFields = array_merge($allowedFields, ['promotions', 'promotions.*.id', 'promotions.*.title', 'promotions.*.description', 'promotions.*.link', 'promotions.*.image', 'promotions.*.active', 'promotions.*.branch_ids', 'promotions.*.branch_ids.*']);
        }
        if ($user?->hasPermission('settings.appearance')) {
            $allowedFields = array_merge($allowedFields, ['theme_primary', 'theme_sidebar', 'theme_sidebar_text', 'theme_accent']);
        }
        if ($user?->hasPermission('settings.payment')) {
            $allowedFields = array_merge($allowedFields, ['xendit_secret_key', 'xendit_public_key', 'xendit_webhook_secret']);
        }

        $rules = array_intersect_key($rules, array_flip($allowedFields));
        $validated = $request->validate($rules);

        $settings = $this->getSettings();
        $oldPromotions = $settings['promotions'] ?? [];

        $settings = array_merge($settings, $validated);

        if (isset($validated['promotions'])) {
            $promotions = array_values(array_filter($validated['promotions'], fn ($p) => ! empty($p['title'])));

            $promotions = array_map(function ($promo) use ($oldPromotions) {
                if (empty($promo['image'])) {
                    $old = collect($oldPromotions)->firstWhere('id', $promo['id']);
                    if ($old && ! empty($old['image'])) {
                        $promo['image'] = $old['image'];
                    }
                }

                $promo['branch_ids'] = array_map('intval', $promo['branch_ids'] ?? []);

                return $promo;
            }, $promotions);

            $settings['promotions'] = $promotions;
        }

        $this->settingService->saveSettings($settings);

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function twoFactorSetup(): JsonResponse
    {
        $user = auth()->user();

        if ($user->hasTwoFactorEnabled()) {
            return response()->json([
                'enabled' => true,
                'qr_code' => null,
                'secret' => null,
            ]);
        }

        $google2fa = new Google2FA;
        $secret = $google2fa->generateSecretKey();

        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'enabled' => false,
            'qr_code' => $qrCode,
            'secret' => $secret,
        ]);
    }

    public function enableTwoFactor(Request $request): JsonResponse
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        $google2fa = new Google2FA;
        $valid = $google2fa->verifyKey($request->secret, $request->code);

        if (! $valid) {
            return response()->json(['error' => 'Kode tidak valid. Silakan coba lagi.'], 422);
        }

        $user->update([
            'two_factor_secret' => encrypt($request->secret),
        ]);

        return response()->json(['success' => true]);
    }

    public function disableTwoFactor(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if (! $user->hasTwoFactorEnabled()) {
            return response()->json(['error' => '2FA belum diaktifkan.'], 422);
        }

        $google2fa = new Google2FA;
        $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code);

        if (! $valid) {
            return response()->json(['error' => 'Kode tidak valid.'], 422);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public static function getSettings(): array
    {
        return app(SettingService::class)->getSettings();
    }
}
