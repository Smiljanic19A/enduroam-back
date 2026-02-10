<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

if (! function_exists('presigned_url')) {
    function presigned_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
    }
}

if (! function_exists('to_s3_path')) {
    function to_s3_path(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (! str_starts_with($value, 'http')) {
            return $value;
        }

        $path = parse_url($value, PHP_URL_PATH);

        return ltrim($path, '/');
    }
}
