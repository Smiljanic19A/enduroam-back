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

    public function translate(Tour $tour, TranslationAIService $service): TourResource|JsonResponse
    {
        $tour->load('includes');

        $content = [
            'name' => $tour->name,
            'description' => $tour->description,
            'full_description' => $tour->full_description ?? '',
            'includes' => $tour->includes->pluck('text')->toArray(),
        ];

        $result = $service->translate($content);

        if (empty($result)) {
            return response()->json(['message' => 'Translation failed. Check logs for details.'], 500);
        }

        DB::transaction(function () use ($tour, $result): void {
            foreach ($result as $locale => $trans) {
                $tour->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $trans['name'],
                        'description' => $trans['description'],
                        'full_description' => $trans['full_description'] ?? null,
                    ]
                );

                if (! empty($trans['includes'])) {
                    foreach ($tour->includes as $index => $include) {
                        if (isset($trans['includes'][$index])) {
                            $include->translations()->updateOrCreate(
                                ['locale' => $locale],
                                ['text' => $trans['includes'][$index]]
                            );
                        }
                    }
                }
            }
        });

        $tour->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'translations']);

        return new TourResource($tour);
    }

    public function translateAll(TranslationAIService $service): JsonResponse
    {
        $tours = Tour::with('includes')->get();
        $translated = 0;
        $failed = 0;
        $errors = [];

        foreach ($tours as $tour) {
            $content = [
                'name' => $tour->name,
                'description' => $tour->description,
                'full_description' => $tour->full_description ?? '',
                'includes' => $tour->includes->pluck('text')->toArray(),
            ];

            $result = $service->translate($content);

            if (empty($result)) {
                $failed++;
                $errors[] = "Tour #{$tour->id} ({$tour->name}): Translation failed";

                continue;
            }

            DB::transaction(function () use ($tour, $result): void {
                foreach ($result as $locale => $trans) {
                    $tour->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $trans['name'],
                            'description' => $trans['description'],
                            'full_description' => $trans['full_description'] ?? null,
                        ]
                    );

                    if (! empty($trans['includes'])) {
                        foreach ($tour->includes as $index => $include) {
                            if (isset($trans['includes'][$index])) {
                                $include->translations()->updateOrCreate(
                                    ['locale' => $locale],
                                    ['text' => $trans['includes'][$index]]
                                );
                            }
                        }
                    }
                }
            });

            $translated++;
        }

        return response()->json([
            'message' => "Translation complete. {$translated} translated, {$failed} failed.",
            'translated' => $translated,
            'failed' => $failed,
            'errors' => $errors,
        ]);
    }
}
