<?php

namespace App\Console\Commands;

use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {dir=products}';
    protected $description = 'Buat versi WebP untuk semua gambar di direktori storage/public';

    public function handle(): int
    {
        $dir = $this->argument('dir');
        $disk = Storage::disk('public');
        $files = $disk->allFiles($dir);
        $converted = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                continue;
            }

            $webpRel = ImageService::webpPath($file);
            if ($disk->exists($webpRel)) {
                $skipped++;
                continue;
            }

            $result = ImageService::ensureWebp($file);
            if ($result) {
                $originalSize = filesize($disk->path($file));
                $webpSize = filesize($disk->path($result));
                $saved = $originalSize - $webpSize;
                $this->line("  <info>✓</info> {$file} ({$this->formatBytes($originalSize)} → {$this->formatBytes($webpSize)}, hemat {$this->formatBytes($saved)})");
                $converted++;
            }
        }

        $this->newLine();
        $this->info("Selesai: {$converted} dikonversi, {$skipped} sudah ada.");

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
