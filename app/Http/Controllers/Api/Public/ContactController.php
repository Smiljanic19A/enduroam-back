<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

final class ContactController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function store(ContactRequest $request): JsonResponse
    {
        $contactMessage = ContactMessage::create($request->validated());

        $this->notificationService->create(
            type: 'new_contact_message',
            title: "New message from {$contactMessage->name}",
            body: $contactMessage->subject,
            data: [
                'contact_message_id' => $contactMessage->id,
                'name' => $contactMessage->name,
                'email' => $contactMessage->email,
            ],
        );

        return response()->json([
            'message' => 'Thank you for your message. We will get back to you soon.',
        ], 201);
    }
}
