<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Services\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds one global promo (the owner's single default, shown on every branch
 * that has no override of its own) plus one branch-specific override, with
 * real banner images copied from database/seeders/images/promotions/ — so
 * the global/override promo behavior can be exercised end-to-end right after
 * a fresh seed instead of needing to be configured by hand first.
 * Run standalone with:
 *   php artisan db:seed --class=PromotionSeeder
 */
class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $settingService = app(SettingService::class);

        $globalImage = $this->seedImage('promo-global-diskon.jpg', 'promotions/seed-global-diskon.jpg');
        $branchImage = $this->seedImage('promo-branch-weekend.jpg', 'promotions/seed-branch-weekend.jpg');

        $settingService->saveSettings([
            'promotions' => [
                [
                    'id' => 1,
                    'title' => 'Diskon 20% Hari Ini',
                    'description' => 'Berlaku untuk semua menu, semua cabang Oribun Bakery.',
                    'link' => '',
                    'image' => $globalImage,
                    'active' => true,
                ],
            ],
        ], null);

        $branch = Branch::where('slug', 'cabang-pulung-kencana')->first();

        if ($branch) {
            $settingService->saveSettings([
                'promotions' => [
                    [
                        'id' => 2,
                        'title' => 'Weekend Bundling Roti',
                        'description' => 'Beli 3 roti pilihan, gratis 1 — khusus akhir pekan.',
                        'link' => '',
                        'image' => $branchImage,
                        'active' => true,
                    ],
                ],
            ], $branch->id);
        }
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
