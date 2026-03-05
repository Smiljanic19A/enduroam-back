<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminQuickEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

final class EmailController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'to' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        Mail::to($data['to'])->queue(
            new AdminQuickEmail($data['subject'], $data['body'])
        );

        return response()->json(['message' => 'Email queued for delivery.']);
    }
}
