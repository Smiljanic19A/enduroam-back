<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Mail\ContactMessageReply;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Mail;

final class ContactMessageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ContactMessage::latest();

        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        $messages = $query->paginate($request->input('per_page', 20));

        return ContactMessageResource::collection($messages);
    }

    public function show(ContactMessage $contactMessage): ContactMessageResource
    {
        if (! $contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        return new ContactMessageResource($contactMessage);
    }

    public function reply(Request $request, ContactMessage $contactMessage): ContactMessageResource
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $contactMessage->update([
            'reply_message' => $data['message'],
            'replied_at' => now(),
        ]);

        Mail::to($contactMessage->email)
            ->send(new ContactMessageReply($contactMessage, $data['message']));

        return new ContactMessageResource($contactMessage->fresh());
    }

    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->delete();

        return response()->json(['message' => 'Message deleted successfully.']);
    }
}
