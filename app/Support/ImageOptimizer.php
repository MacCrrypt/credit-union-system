<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    public static function storeSignature(UploadedFile $file, ?string $disk = null, string $directory = 'signatures'): string
    {
        $disk ??= config('filesystems.signature_cards.disk', 'local');

        // If GD is unavailable, we keep the upload flow working by falling back
        // to the normal store method instead of failing the request.
        if (! extension_loaded('gd')) {
            return $file->store($directory, $disk);
        }

        $imageInfo = @getimagesize($file->getRealPath());

        if (! $imageInfo) {
            return $file->store($directory, $disk);
        }

        [$width, $height, $imageType] = $imageInfo;

        $sourceImage = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => @imagecreatefrompng($file->getRealPath()),
            default => null,
        };

        if (! $sourceImage) {
            return $file->store($directory, $disk);
        }

        // Signature cards do not need original camera resolution, so we cap the
        // dimensions to reduce long-term storage growth.
        $maxWidth = 1400;
        $maxHeight = 700;
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $optimizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($optimizedImage, false);
            imagesavealpha($optimizedImage, true);
            $transparent = imagecolorallocatealpha($optimizedImage, 0, 0, 0, 127);
            imagefilledrectangle($optimizedImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled(
            $optimizedImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );

        $extension = $imageType === IMAGETYPE_PNG ? 'png' : 'jpg';
        $relativePath = trim($directory, '/') . '/' . Str::uuid() . '.' . $extension;
        Storage::disk($disk)->makeDirectory($directory);
        $absolutePath = Storage::disk($disk)->path($relativePath);

        $stored = $imageType === IMAGETYPE_PNG
            ? imagepng($optimizedImage, $absolutePath, 6)
            : imagejpeg($optimizedImage, $absolutePath, 75);

        // Free image resources immediately to avoid leaking memory during uploads.
        imagedestroy($sourceImage);
        imagedestroy($optimizedImage);

        if (! $stored) {
            return $file->store($directory, $disk);
        }

        return $relativePath;
    }
}
