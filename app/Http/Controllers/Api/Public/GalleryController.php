<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryImageResource;
use App\Models\GalleryImage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class GalleryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $images = GalleryImage::ordered()->get();

        return GalleryImageResource::collection($images);
    }
}
