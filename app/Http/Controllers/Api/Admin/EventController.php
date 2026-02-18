<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Jobs\TranslateContentJob;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

final class EventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = Event::with(['includes', 'images', 'availableDates', 'approvedReviews', 'translations'])
            ->ordered()
            ->get();

        return EventResource::collection($events);
    }

    public function store(StoreEventRequest $request): EventResource
    {
        $event = DB::transaction(function () use ($request): Event {
            $data = $request->validated();
            $includes = $data['includes'] ?? [];
            $images = $data['images'] ?? [];
            $availableDates = $data['available_dates'] ?? [];
            $translations = $data['translations'] ?? [];

            unset($data['includes'], $data['images'], $data['available_dates'], $data['translations']);

            if (! isset($data['spots_left'])) {
                $data['spots_left'] = $data['max_participants'];
            }

            $event = Event::create($data);

            foreach ($includes as $include) {
                $includeTranslations = $include['translations'] ?? [];
                unset($include['translations']);

                $createdInclude = $event->includes()->create($include);

                foreach ($includeTranslations as $locale => $trans) {
                    $createdInclude->translations()->create([
                        'locale' => $locale,
                        'text' => $trans['text'],
                    ]);
                }
            }

            foreach ($images as $image) {
                $event->images()->create($image);
            }

            foreach ($availableDates as $date) {
                $event->availableDates()->create(['date' => $date]);
            }

            foreach ($translations as $locale => $trans) {
                $event->translations()->create([
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'description' => $trans['description'],
                    'full_description' => $trans['full_description'] ?? null,
                ]);
            }

            return $event;
        });

        $event->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'translations']);

        return new EventResource($event);
    }

    public function show(Event $event): EventResource
    {
        $event->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'bookings', 'translations']);

        return new EventResource($event);
    }

    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        DB::transaction(function () use ($request, $event): void {
            $data = $request->validated();
            $includes = $data['includes'] ?? null;
            $images = $data['images'] ?? null;
            $availableDates = $data['available_dates'] ?? null;
            $translations = $data['translations'] ?? null;

            unset($data['includes'], $data['images'], $data['available_dates'], $data['translations']);

            $event->update($data);

            if ($includes !== null) {
                $event->includes()->delete();
                foreach ($includes as $include) {
                    $includeTranslations = $include['translations'] ?? [];
                    unset($include['translations']);

                    $createdInclude = $event->includes()->create($include);

                    foreach ($includeTranslations as $locale => $trans) {
                        $createdInclude->translations()->create([
                            'locale' => $locale,
                            'text' => $trans['text'],
                        ]);
                    }
                }
            }

            if ($images !== null) {
                $event->images()->delete();
                foreach ($images as $image) {
                    $event->images()->create($image);
                }
            }

            if ($availableDates !== null) {
                $event->availableDates()->delete();
                foreach ($availableDates as $date) {
                    $event->availableDates()->create(['date' => $date]);
                }
            }

            if ($translations !== null) {
                $event->translations()->delete();
                foreach ($translations as $locale => $trans) {
                    $event->translations()->create([
                        'locale' => $locale,
                        'name' => $trans['name'],
                        'description' => $trans['description'],
                        'full_description' => $trans['full_description'] ?? null,
                    ]);
                }
            }
        });

        $event->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'translations']);

        return new EventResource($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.']);
    }

    public function setFeatured(Event $event): EventResource
    {
        DB::transaction(function () use ($event): void {
            Event::where('is_featured', true)->update(['is_featured' => false]);
            $event->update(['is_featured' => true]);
        });

        $event->load(['includes', 'images', 'approvedReviews', 'translations']);

        return new EventResource($event);
    }

    public function translate(Event $event): JsonResponse
    {
        TranslateContentJob::dispatch(Event::class, [$event->id]);

        return response()->json(['message' => 'Translation started. You will be notified when it completes.']);
    }

    public function translateAll(): JsonResponse
    {
        TranslateContentJob::dispatch(Event::class);

        return response()->json(['message' => 'Translating all events in the background. You will be notified when it completes.']);
    }
}
