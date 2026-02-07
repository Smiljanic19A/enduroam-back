<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

final class UploadController extends Controller
{
    public function store(UploadRequest $request): JsonResponse
    {
        $folder = $request->input('folder', 'uploads');
        $file = $request->file('file');

        $path = $file->store($folder, 's3');

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('s3')->url($path),
        ], 201);
    }

    public function destroy(): JsonResponse
    {
        $path = request()->input('path');

        if ($path && Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);

            return response()->json(['message' => 'File deleted successfully.']);
        }

        return response()->json(['message' => 'File not found.'], 404);
    }
}
