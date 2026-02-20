<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

final class ImageProxyController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $path = $request->query('path', '');

        if (empty($path) || str_contains($path, '..')) {
            abort(400, 'Invalid path.');
        }

        $disk = Storage::disk('s3');

        if (! $disk->exists($path)) {
            abort(404, 'Image not found.');
        }

        return response($disk->get($path), 200, [
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
