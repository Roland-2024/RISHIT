<?php

namespace App\Domain\Catalog;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

final class ListingImageStorage
{
    public const MAX_FILES = 8;

    public const MAX_KILOBYTES = 8192;

    public const MIN_DIMENSION = 320;

    public const MAX_DIMENSION = 4096;

    public function store(UploadedFile $file): string
    {
        $details = $this->details($file);
        $image = match ($details['mime']) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($file->getRealPath()) : false,
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($file->getRealPath()) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : false,
            default => false,
        };

        if (! $image instanceof GdImage) {
            $this->invalid();
        }

        try {
            $image = $this->orient($image, $file, $details['mime']);
            $encoded = $this->encode($image, $details['mime']);
            $extension = match ($details['mime']) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            };
            $path = 'listings/'.Str::uuid().'.'.$extension;

            if (! Storage::disk($this->disk())->put($path, $encoded)) {
                Storage::disk($this->disk())->delete($path);

                throw new RuntimeException('The listing image could not be stored.');
            }

            return $path;
        } finally {
            imagedestroy($image);
        }
    }

    /** @param string|array<int, string> $paths */
    public function delete(string|array $paths): void
    {
        Storage::disk($this->disk())->delete($paths);
    }

    public function url(string $path): string
    {
        return Storage::disk($this->disk())->url($path);
    }

    /** @return array{0: int, 1: int, mime: string} */
    private function details(UploadedFile $file): array
    {
        $details = $file->isValid() ? @getimagesize($file->getRealPath()) : false;

        if (
            $details === false
            || ! in_array($details['mime'] ?? null, ['image/jpeg', 'image/png', 'image/webp'], true)
            || $file->getSize() > self::MAX_KILOBYTES * 1024
            || $details[0] < self::MIN_DIMENSION
            || $details[1] < self::MIN_DIMENSION
            || $details[0] > self::MAX_DIMENSION
            || $details[1] > self::MAX_DIMENSION
        ) {
            $this->invalid();
        }

        return $details;
    }

    private function orient(GdImage $image, UploadedFile $file, string $mime): GdImage
    {
        if ($mime !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($file->getRealPath(), 'IFD0', true);
        $orientation = (int) ($exif['IFD0']['Orientation'] ?? $exif['Orientation'] ?? 1);

        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, in_array($orientation, [2, 5], true) ? IMG_FLIP_HORIZONTAL : IMG_FLIP_VERTICAL);
        }

        $angle = match ($orientation) {
            3 => 180,
            5, 8 => 90,
            6, 7 => -90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);

        if (! $rotated instanceof GdImage) {
            throw new RuntimeException('The listing image orientation could not be normalized.');
        }

        imagedestroy($image);

        return $rotated;
    }

    private function encode(GdImage $image, string $mime): string
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        ob_start();

        $encoded = match ($mime) {
            'image/jpeg' => imagejpeg($image, null, 85),
            'image/png' => imagepng($image, null, 6),
            'image/webp' => function_exists('imagewebp') && imagewebp($image, null, 85),
        };
        $contents = ob_get_clean();

        if (! $encoded || $contents === false || $contents === '') {
            throw new RuntimeException('The listing image could not be safely re-encoded.');
        }

        return $contents;
    }

    private function disk(): string
    {
        return config('marketplace.listing_image_disk', 'public');
    }

    private function invalid(): never
    {
        throw ValidationException::withMessages(['photos' => __('validation.image')]);
    }
}
