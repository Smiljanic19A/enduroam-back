<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryImageResource;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class GalleryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $images = GalleryImage::ordered()->get();

        return GalleryImageResource::collection($images);
    }

    public function store(Request $request): GalleryImageResource
    {
        $data = $request->validate([
            'src' => ['required', 'string', 'max:2048'],
            'alt' => ['nullable', 'string', 'max:255'],
            'aspect_ratio' => ['sometimes', 'string', 'in:landscape,portrait,square'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $image = GalleryImage::create($data);

        return new GalleryImageResource($image);
    }

    public function show(GalleryImage $galleryImage): GalleryImageResource
    {
        return new GalleryImageResource($galleryImage);
    }

    public function update(Request $request, GalleryImage $galleryImage): GalleryImageResource
    {
        $data = $request->validate([
            'src' => ['sometimes', 'string', 'max:2048'],
            'alt' => ['nullable', 'string', 'max:255'],
            'aspect_ratio' => ['sometimes', 'string', 'in:landscape,portrait,square'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $galleryImage->update($data);

        return new GalleryImageResource($galleryImage->fresh());
    }

    public function destroy(GalleryImage $galleryImage): JsonResponse
    {
        $galleryImage->delete();

        return response()->json(['message' => 'Gallery image deleted successfully.']);
    }
}
