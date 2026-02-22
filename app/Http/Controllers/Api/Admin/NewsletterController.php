<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterBroadcast;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

final class NewsletterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subscribers = NewsletterSubscriber::latest()
            ->paginate($request->input('per_page', 20));

        return response()->json($subscribers);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $subscribers = NewsletterSubscriber::all();
        $count = 0;

        foreach ($subscribers as $subscriber) {
            $unsubscribeUrl = URL::signedRoute('public.newsletter.unsubscribe', [
                'subscriber' => $subscriber->id,
            ]);

            Mail::to($subscriber->email)
                ->send(new NewsletterBroadcast($data['subject'], $data['body'], $unsubscribeUrl));

            $count++;
        }

        return response()->json([
            'message' => "Newsletter sent to {$count} subscriber(s).",
            'count' => $count,
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return response()->json(['message' => 'Subscriber removed successfully.']);
    }
}
