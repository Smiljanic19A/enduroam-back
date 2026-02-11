<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class EventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = Event::active()
            ->ordered()
            ->with(['includes', 'images', 'approvedReviews'])
            ->get();

        return EventResource::collection($events);
    }

    public function show(Event $event): EventResource
    {
        $event->load(['includes', 'images', 'approvedReviews']);

        return new EventResource($event);
    }

    public function featured(): EventResource
    {
        $event = Event::active()
            ->where('is_featured', true)
            ->with(['includes', 'images', 'approvedReviews'])
            ->firstOrFail();

        return new EventResource($event);
    }
}
