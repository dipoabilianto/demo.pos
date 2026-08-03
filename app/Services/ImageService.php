<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public static function webpPath(string $path): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . '/' . $info['filename'] . '.webp';
    }

    public static function ensureWebp(string $path): ?string
    {
        $fullPath = Storage::disk('public')->path($path);
        if (!file_exists($fullPath)) return null;

        $webpRelPath = self::webpPath($path);
        $webpFullPath = Storage::disk('public')->path($webpRelPath);
        if (file_exists($webpFullPath)) return $webpRelPath;

        $mime = mime_content_type($fullPath);
        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($fullPath),
            'image/png' => imagecreatefrompng($fullPath),
            'image/gif' => imagecreatefromgif($fullPath),
            default => null,
        };

        if (!$image) return null;

        imagewebp($image, $webpFullPath, 80);
        imagedestroy($image);

        return $webpRelPath;
    }

    public static function url(string $originalPath): string
    {
        if ($webp = self::ensureWebp($originalPath)) {
            return Storage::disk('public')->url($webp);
        }
        return Storage::disk('public')->url($originalPath);
    }
}
