<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Convert uploaded image to WebP and store on the public disk.
     * Output always .webp regardless of input format (jpg/png/webp).
     * Returns the storage-relative path (e.g. "covers/123.webp").
     */
    public function storeAsWebp(UploadedFile $file, string $folder, string $name, int $quality = 80): string
    {
        $src = $this->createFromFile($file);

        if (!$src) {
            throw new \RuntimeException('Unsupported image format. Allowed: JPG, PNG, WebP.');
        }

        Storage::disk('public')->makeDirectory($folder);

        $filename = $name . '.webp';
        $fullPath = Storage::disk('public')->path($folder . '/' . $filename);

        imagewebp($src, $fullPath, $quality);

        imagedestroy($src);

        return $folder . '/' . $filename;
    }

    /**
     * Delete an image from the public disk.
     * Returns true if file existed and was deleted, false otherwise.
     */
    public function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        return Storage::disk('public')->delete($path);
    }

    private function createFromFile(UploadedFile $file)
    {
        $type = @exif_imagetype($file->getPathname());

        return match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file->getPathname()),
            IMAGETYPE_PNG  => $this->createFromPng($file->getPathname()),
            IMAGETYPE_WEBP => @imagecreatefromwebp($file->getPathname()),
            default        => null,
        };
    }

    private function createFromPng(string $path)
    {
        $img = @imagecreatefrompng($path);
        if ($img) {
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }
        return $img;
    }
}