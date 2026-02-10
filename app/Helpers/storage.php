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
