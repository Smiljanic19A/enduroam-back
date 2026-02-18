<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TourController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $tours = Tour::active()
            ->ordered()
            ->with(['includes.translations', 'images', 'approvedReviews', 'translations'])
            ->get();

        return TourResource::collection($tours);
    }

    public function show(Tour $tour): TourResource
    {
        $tour->load(['includes.translations', 'images', 'availableDates', 'approvedReviews', 'translations']);

        return new TourResource($tour);
    }
}
