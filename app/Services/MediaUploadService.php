<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    public function __construct(
        private SettingService $settingService,
    ) {}

    public function uploadLogo(Request $request): array
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,gif|max:1024',
        ]);

        $path = $request->file('logo')->store('logos', 'public');
        $settings = $this->settingService->getSettings();
        $settings['receipt_logo'] = $path;
        $this->settingService->saveSettings($settings);

        return ['success' => true, 'path' => $path];
    }

    public function uploadPromoImage(Request $request): array
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'promo_id' => 'required|integer',
        ]);

        $path = $request->file('image')->store('promotions', 'public');
        $settings = $this->settingService->getSettings();

        if (isset($settings['promotions'])) {
            foreach ($settings['promotions'] as &$promo) {
                if (($promo['id'] ?? null) == $request->promo_id) {
                    $promo['image'] = $path;
                    break;
                }
            }
        }

        $this->settingService->saveSettings($settings);

        return ['success' => true, 'path' => $path];
    }

    public function uploadFavicon(Request $request): array
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,jpg,jpeg,ico|max:512',
        ]);

        $path = $request->file('favicon')->store('favicons', 'public');
        $settings = $this->settingService->getSettings();
        $settings['favicon'] = $path;
        $this->settingService->saveSettings($settings);

        return ['success' => true, 'path' => asset('storage/'.$path)];
    }

    public function uploadNotificationSound(Request $request): array
    {
        $request->validate([
            'file' => 'required|file|mimes:mp3,wav,ogg|max:2048',
        ]);

        $path = $request->file('file')->store('notification-sounds', 'public');
        $settings = $this->settingService->getSettings();
        $settings['notification_sound_file'] = $path;
        $settings['notification_sound_preset'] = 'custom';
        $this->settingService->saveSettings($settings);

        return [
            'success' => true,
            'path' => asset('storage/'.$path),
            'filename' => $request->file('file')->getClientOriginalName(),
        ];
    }

    public function uploadLoginLogo(Request $request): array
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,webp|max:1024',
        ]);

        $path = $request->file('image')->store('logos', 'public');
        $settings = $this->settingService->getSettings();
        $settings['login_logo'] = $path;
        $this->settingService->saveSettings($settings);

        return [
            'success' => true,
            'path' => asset('storage/'.$path),
        ];
    }

    public function uploadLoginBackground(Request $request): array
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
        ]);

        $path = $request->file('image')->store('login-backgrounds', 'public');
        $settings = $this->settingService->getSettings();
        $settings['login_background'] = $path;
        $this->settingService->saveSettings($settings);

        return [
            'success' => true,
            'path' => asset('storage/'.$path),
        ];
    }
}
