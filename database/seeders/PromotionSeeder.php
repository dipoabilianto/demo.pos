<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Services\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds 3 promos to demonstrate the per-promo branch targeting: one with no
 * branch_ids (applies to every branch) and two each scoped to one specific
 * branch, with real banner images copied from
 * database/seeders/images/promotions/. Run standalone with:
 *   php artisan db:seed --class=PromotionSeeder
 */
class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $settingService = app(SettingService::class);

        $utama = Branch::where('slug', 'cabang-utama')->first();
        $pulungKencana = Branch::where('slug', 'cabang-pulung-kencana')->first();

        $promotions = [
            [
                'id' => 1,
                'title' => 'Promo Weekend',
                'description' => 'Diskon 20% untuk semua menu, akhir pekan ini saja.',
                'link' => '',
                'image' => $this->seedImage('promo-weekend.jpg', 'promotions/seed-promo-weekend.jpg'),
                'active' => true,
                'branch_ids' => [], // kosong = berlaku di semua cabang
            ],
        ];

        if ($pulungKencana) {
            $promotions[] = [
                'id' => 2,
                'title' => 'Promo Roti',
                'description' => 'Beli 3 roti pilihan, gratis 1 — khusus cabang ini.',
                'link' => '',
                'image' => $this->seedImage('promo-roti.jpg', 'promotions/seed-promo-roti.jpg'),
                'active' => true,
                'branch_ids' => [$pulungKencana->id],
            ];
        }

        if ($utama) {
            $promotions[] = [
                'id' => 3,
                'title' => 'Promo Kopi',
                'description' => 'Beli 2 kopi, gratis 1 topping favorit — khusus cabang ini.',
                'link' => '',
                'image' => $this->seedImage('promo-kopi.jpg', 'promotions/seed-promo-kopi.jpg'),
                'active' => true,
                'branch_ids' => [$utama->id],
            ];
        }

        $settingService->saveSettings(['promotions' => $promotions]);
    }

    private function seedImage(string $sourceFilename, string $storagePath): string
    {
        if (! Storage::disk('public')->exists($storagePath)) {
            $source = database_path('seeders/images/promotions/'.$sourceFilename);
            Storage::disk('public')->put($storagePath, file_get_contents($source));
        }

        return $storagePath;
    }
}
