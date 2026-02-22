<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsletterRequest;
use App\Models\NewsletterSubscriber;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NewsletterController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function subscribe(NewsletterRequest $request): JsonResponse
    {
        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $request->validated('email')],
        );

        if ($subscriber->wasRecentlyCreated) {
            $this->notificationService->create(
                type: 'new_newsletter_subscriber',
                title: 'New newsletter subscriber',
                body: $subscriber->email,
                data: [
                    'subscriber_id' => $subscriber->id,
                    'email' => $subscriber->email,
                ],
            );
        }

        return response()->json([
            'message' => 'You have been subscribed to our newsletter.',
        ], 201);
    }

    public function unsubscribe(Request $request, NewsletterSubscriber $subscriber): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired unsubscribe link.');
        }

        $subscriber->delete();

        return response()->json([
            'message' => 'You have been unsubscribed from our newsletter.',
        ]);
    }
}
