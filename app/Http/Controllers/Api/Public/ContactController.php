<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageForward;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

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

        $contactEmail = SiteSetting::getValue('contact_email', config('mail.from.address'));
        Mail::to($contactEmail)
            ->send(new ContactMessageForward($contactMessage));

        return response()->json([
            'message' => 'Thank you for your message. We will get back to you soon.',
        ], 201);
    }
}
