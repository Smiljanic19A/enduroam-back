<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsletterRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

final class NewsletterController extends Controller
{
    public function subscribe(NewsletterRequest $request): JsonResponse
    {
        NewsletterSubscriber::firstOrCreate(
            ['email' => $request->validated('email')],
        );

        return response()->json([
            'message' => 'You have been subscribed to our newsletter.',
        ], 201);
    }
}
