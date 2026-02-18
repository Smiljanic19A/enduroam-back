<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

final class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): JsonResponse
    {
        Review::create([
            'author' => $request->validated('author'),
            'rating' => $request->validated('rating'),
            'text' => $request->validated('text'),
            'date' => now(),
            'is_approved' => false,
            'reviewable_type' => 'general',
            'reviewable_id' => 0,
        ]);

        return response()->json(['message' => 'Thank you for your review! It will be visible after approval.'], 201);
    }
}
