<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class BannerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $banners = Banner::ordered()->get();

        return BannerResource::collection($banners);
    }

    public function store(StoreBannerRequest $request): BannerResource
    {
        $banner = Banner::create($request->validated());

        return new BannerResource($banner);
    }

    public function show(Banner $banner): BannerResource
    {
        return new BannerResource($banner);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): BannerResource
    {
        $banner->update($request->validated());

        return new BannerResource($banner->fresh());
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $banner->delete();

        return response()->json(['message' => 'Banner deleted successfully.']);
    }
}
