<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
use App\Jobs\TranslateBannerJob;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class BannerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $banners = Banner::with('translations')->ordered()->get();

        return BannerResource::collection($banners);
    }

    public function store(StoreBannerRequest $request): BannerResource
    {
        $validated = $request->validated();
        $translationsInput = $validated['translations'] ?? null;
        unset($validated['translations']);

        $banner = Banner::create($validated);

        if (is_array($translationsInput)) {
            $this->saveTranslations($banner, $translationsInput);
        }

        return new BannerResource($banner->load('translations'));
    }

    public function show(Banner $banner): BannerResource
    {
        return new BannerResource($banner->load('translations'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner): BannerResource
    {
        $validated = $request->validated();
        $translationsInput = $validated['translations'] ?? null;
        unset($validated['translations']);

        $banner->update($validated);

        if (is_array($translationsInput)) {
            $this->saveTranslations($banner, $translationsInput);
        }

        return new BannerResource($banner->fresh()->load('translations'));
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $banner->delete();

        return response()->json(['message' => 'Banner deleted successfully.']);
    }

    public function translate(Banner $banner): JsonResponse
    {
        TranslateBannerJob::dispatch($banner->id);

        return response()->json(['message' => 'Translation queued.'], 202);
    }

    /**
     * @param  array<string, array{title?: string, text?: string, cta_text?: string}>  $translations
     */
    private function saveTranslations(Banner $banner, array $translations): void
    {
        foreach ($translations as $locale => $trans) {
            if (! is_array($trans)) {
                continue;
            }

            $banner->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $trans['title'] ?? null,
                    'text' => $trans['text'] ?? null,
                    'cta_text' => $trans['cta_text'] ?? null,
                ]
            );
        }
    }
}
