<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class EventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = Event::active()
            ->ordered()
            ->with(['includes.translations', 'images', 'translations'])
            ->get();

        return EventResource::collection($events);
    }

    public function show(Event $event): EventResource
    {
        $event->load(['includes.translations', 'images', 'availableDates', 'translations']);

        return new EventResource($event);
    }

    public function featured(): EventResource|JsonResponse
    {
        $event = Event::active()
            ->where('is_featured', true)
            ->with(['includes.translations', 'images', 'translations'])
            ->first();

        if (! $event) {
            return response()->json(['message' => 'No featured event set.'], 204);
        }

        return new EventResource($event);
    }
}
