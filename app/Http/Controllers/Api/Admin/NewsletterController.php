<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NewsletterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subscribers = NewsletterSubscriber::latest()
            ->paginate($request->input('per_page', 20));

        return response()->json($subscribers);
    }

    public function destroy(NewsletterSubscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return response()->json(['message' => 'Subscriber removed successfully.']);
    }
}
