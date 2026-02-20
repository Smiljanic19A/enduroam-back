<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class BannerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $banners = Banner::with('translations')
            ->active()
            ->ordered()
            ->get();

        return BannerResource::collection($banners);
    }
}
