<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

final class EventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = Event::with(['includes', 'images', 'approvedReviews'])
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

            unset($data['includes'], $data['images']);

            if (! isset($data['spots_left'])) {
                $data['spots_left'] = $data['max_participants'];
            }

            $event = Event::create($data);

            foreach ($includes as $include) {
                $event->includes()->create($include);
            }

            foreach ($images as $image) {
                $event->images()->create($image);
            }

            return $event;
        });

        $event->load(['includes', 'images', 'approvedReviews']);

        return new EventResource($event);
    }

    public function show(Event $event): EventResource
    {
        $event->load(['includes', 'images', 'approvedReviews', 'bookings']);

        return new EventResource($event);
    }

    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        DB::transaction(function () use ($request, $event): void {
            $data = $request->validated();
            $includes = $data['includes'] ?? null;
            $images = $data['images'] ?? null;

            unset($data['includes'], $data['images']);

            $event->update($data);

            if ($includes !== null) {
                $event->includes()->delete();
                foreach ($includes as $include) {
                    $event->includes()->create($include);
                }
            }

            if ($images !== null) {
                $event->images()->delete();
                foreach ($images as $image) {
                    $event->images()->create($image);
                }
            }
        });

        $event->load(['includes', 'images', 'approvedReviews']);

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

        $event->load(['includes', 'images', 'approvedReviews']);

        return new EventResource($event);
    }
}
