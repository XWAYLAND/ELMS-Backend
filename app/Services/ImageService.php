<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload file gambar cover dan konversi ke format WebP (kualitas 80%).
     * File disimpan di storage/app/public/covers/.
     *
     * @param  UploadedFile  $file
     * @return string  Path relatif dari storage public (contoh: "covers/abc123.webp")
     */
    public function uploadCover(UploadedFile $file): string
    {
        $filename = Str::uuid()->toString() . '.webp';
        $storagePath = 'covers/' . $filename;

        // Coba konversi ke WebP jika ekstensi GD tersedia
        if (extension_loaded('gd') && function_exists('imagewebp')) {
            $webpContent = $this->convertToWebp($file->getRealPath(), $file->getMimeType());

            if ($webpContent !== null) {
                Storage::disk('public')->put($storagePath, $webpContent);
                return $storagePath;
            }
        }

        // Fallback: simpan file asli tanpa konversi
        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $fallbackFilename = Str::uuid()->toString() . '.' . $extension;
        $fallbackPath = 'covers/' . $fallbackFilename;

        $file->storeAs('covers', $fallbackFilename, 'public');

        return $fallbackPath;
    }

    /**
     * Hapus file cover dari storage (hanya untuk file lokal, bukan URL eksternal).
     *
     * @param  string|null  $path  Path relatif dari storage public
     */
    public function deleteCover(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        // Jangan hapus jika merupakan URL eksternal
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    /**
     * Konversi file gambar ke konten binary WebP menggunakan GD.
     *
     * @param  string  $realPath   Path absolute file di filesystem sementara
     * @param  string  $mimeType   MIME type file asli
     * @return string|null         Konten binary WebP, atau null jika gagal
     */
    private function convertToWebp(string $realPath, string $mimeType): ?string
    {
        $image = match ($mimeType) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($realPath),
            'image/png'               => @imagecreatefrompng($realPath),
            'image/webp'              => @imagecreatefromwebp($realPath),
            'image/gif'               => @imagecreatefromgif($realPath),
            default                   => null,
        };

        if (! $image) {
            return null;
        }

        // Pertahankan transparansi untuk PNG/GIF
        if (in_array($mimeType, ['image/png', 'image/gif'])) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        ob_start();
        $success = imagewebp($image, null, 80); // Kualitas 80%
        $content = ob_get_clean();

        imagedestroy($image);

        return ($success && $content) ? $content : null;
    }
}
