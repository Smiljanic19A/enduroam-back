<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

final class ContactController extends Controller
{
    public function store(ContactRequest $request): JsonResponse
    {
        ContactMessage::create($request->validated());

        return response()->json([
            'message' => 'Thank you for your message. We will get back to you soon.',
        ], 201);
    }
}
