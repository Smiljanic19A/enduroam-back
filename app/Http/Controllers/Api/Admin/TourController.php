<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

final class TourController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $tours = Tour::with(['includes', 'images', 'unavailableDates', 'approvedReviews'])
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
            $unavailableDates = $data['unavailable_dates'] ?? [];

            unset($data['includes'], $data['images'], $data['unavailable_dates']);

            $tour = Tour::create($data);

            foreach ($includes as $include) {
                $tour->includes()->create($include);
            }

            foreach ($images as $image) {
                $tour->images()->create($image);
            }

            foreach ($unavailableDates as $date) {
                $tour->unavailableDates()->create(['date' => $date]);
            }

            return $tour;
        });

        $tour->load(['includes', 'images', 'unavailableDates', 'approvedReviews']);

        return new TourResource($tour);
    }

    public function show(Tour $tour): TourResource
    {
        $tour->load(['includes', 'images', 'unavailableDates', 'approvedReviews', 'bookings']);

        return new TourResource($tour);
    }

    public function update(UpdateTourRequest $request, Tour $tour): TourResource
    {
        DB::transaction(function () use ($request, $tour): void {
            $data = $request->validated();
            $includes = $data['includes'] ?? null;
            $images = $data['images'] ?? null;
            $unavailableDates = $data['unavailable_dates'] ?? null;

            unset($data['includes'], $data['images'], $data['unavailable_dates']);

            $tour->update($data);

            if ($includes !== null) {
                $tour->includes()->delete();
                foreach ($includes as $include) {
                    $tour->includes()->create($include);
                }
            }

            if ($images !== null) {
                $tour->images()->delete();
                foreach ($images as $image) {
                    $tour->images()->create($image);
                }
            }

            if ($unavailableDates !== null) {
                $tour->unavailableDates()->delete();
                foreach ($unavailableDates as $date) {
                    $tour->unavailableDates()->create(['date' => $date]);
                }
            }
        });

        $tour->load(['includes', 'images', 'unavailableDates', 'approvedReviews']);

        return new TourResource($tour);
    }

    public function destroy(Tour $tour): JsonResponse
    {
        $tour->delete();

        return response()->json(['message' => 'Tour deleted successfully.']);
    }
}
