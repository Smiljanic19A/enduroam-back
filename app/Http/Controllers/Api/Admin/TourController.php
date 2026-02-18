<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Resources\TourResource;
use App\Jobs\TranslateContentJob;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

final class TourController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $tours = Tour::with(['includes', 'images', 'availableDates', 'approvedReviews', 'translations'])
            ->ordered()
            ->get();

        return TourResource::collection($tours);
    }

    public function store(StoreTourRequest $request): TourResource
    {
        $tour = DB::transaction(function () use ($request): Tour {
            $data = $request->validated();
            $includes = $data['includes'] ?? [];
            $images = $data['images'] ?? [];
            $availableDates = $data['available_dates'] ?? [];
            $translations = $data['translations'] ?? [];

            unset($data['includes'], $data['images'], $data['available_dates'], $data['translations']);

            $tour = Tour::create($data);

            foreach ($includes as $include) {
                $includeTranslations = $include['translations'] ?? [];
                unset($include['translations']);

                $createdInclude = $tour->includes()->create($include);

                foreach ($includeTranslations as $locale => $trans) {
                    $createdInclude->translations()->create([
                        'locale' => $locale,
                        'text' => $trans['text'],
                    ]);
                }
            }

            foreach ($images as $image) {
                $tour->images()->create($image);
            }

            foreach ($availableDates as $date) {
                $tour->availableDates()->create(['date' => $date]);
            }

            foreach ($translations as $locale => $trans) {
                $tour->translations()->create([
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'description' => $trans['description'],
                    'full_description' => $trans['full_description'] ?? null,
                ]);
            }

            return $tour;
        });

        $tour->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'translations']);

        return new TourResource($tour);
    }

    public function show(Tour $tour): TourResource
    {
        $tour->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'bookings', 'translations']);

        return new TourResource($tour);
    }

    public function update(UpdateTourRequest $request, Tour $tour): TourResource
    {
        DB::transaction(function () use ($request, $tour): void {
            $data = $request->validated();
            $includes = $data['includes'] ?? null;
            $images = $data['images'] ?? null;
            $availableDates = $data['available_dates'] ?? null;
            $translations = $data['translations'] ?? null;

            unset($data['includes'], $data['images'], $data['available_dates'], $data['translations']);

            $tour->update($data);

            if ($includes !== null) {
                $tour->includes()->delete();
                foreach ($includes as $include) {
                    $includeTranslations = $include['translations'] ?? [];
                    unset($include['translations']);

                    $createdInclude = $tour->includes()->create($include);

                    foreach ($includeTranslations as $locale => $trans) {
                        $createdInclude->translations()->create([
                            'locale' => $locale,
                            'text' => $trans['text'],
                        ]);
                    }
                }
            }

            if ($images !== null) {
                $tour->images()->delete();
                foreach ($images as $image) {
                    $tour->images()->create($image);
                }
            }

            if ($availableDates !== null) {
                $tour->availableDates()->delete();
                foreach ($availableDates as $date) {
                    $tour->availableDates()->create(['date' => $date]);
                }
            }

            if ($translations !== null) {
                $tour->translations()->delete();
                foreach ($translations as $locale => $trans) {
                    $tour->translations()->create([
                        'locale' => $locale,
                        'name' => $trans['name'],
                        'description' => $trans['description'],
                        'full_description' => $trans['full_description'] ?? null,
                    ]);
                }
            }
        });

        $tour->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'translations']);

        return new TourResource($tour);
    }

    public function destroy(Tour $tour): JsonResponse
    {
        $tour->delete();

        return response()->json(['message' => 'Tour deleted successfully.']);
    }

    public function translate(Tour $tour): JsonResponse
    {
        TranslateContentJob::dispatch(Tour::class, [$tour->id]);

        return response()->json(['message' => 'Translation started. You will be notified when it completes.']);
    }

    public function translateAll(): JsonResponse
    {
        TranslateContentJob::dispatch(Tour::class);

        return response()->json(['message' => 'Translating all tours in the background. You will be notified when it completes.']);
    }
}
