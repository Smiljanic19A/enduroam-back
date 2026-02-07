<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ReviewController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Review::with('reviewable')->latest();

        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->boolean('is_approved'));
        }

        $reviews = $query->paginate($request->input('per_page', 20));

        return ReviewResource::collection($reviews);
    }

    public function show(Review $review): ReviewResource
    {
        $review->load('reviewable');

        return new ReviewResource($review);
    }

    public function approve(Review $review): ReviewResource
    {
        $review->update(['is_approved' => true]);

        return new ReviewResource($review->fresh());
    }

    public function reject(Review $review): ReviewResource
    {
        $review->update(['is_approved' => false]);

        return new ReviewResource($review->fresh());
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }
}
