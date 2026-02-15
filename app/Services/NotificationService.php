<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class NotificationService
{
    public function create(string $type, string $title, ?string $body = null, ?array $data = null): Notification
    {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }

    public function all(): Collection
    {
        return Notification::latest()->get();
    }

    public function stream(): StreamedResponse
    {
        $response = new StreamedResponse(function (): void {
            set_time_limit(0);

            // Kill ALL output buffering levels
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            ob_implicit_flush(true);

            // Force headers out with a comment
            echo ": connected\n\n";
            flush();

            // Send initial batch
            $notifications = Notification::orderBy('id')->get();
            $lastId = 0;

            if ($notifications->isNotEmpty()) {
                $lastId = $notifications->last()->id;
                $payload = $notifications->map(fn (Notification $n) => [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'body' => $n->body,
                    'data' => $n->data,
                    'createdAt' => $n->created_at?->toISOString(),
                ])->toArray();

                echo "event: initial\n";
                echo 'data: '.json_encode($payload)."\n\n";
            } else {
                echo "event: initial\n";
                echo "data: []\n\n";
            }

            flush();

            $heartbeatCounter = 0;
            $startTime = time();
            $maxDuration = 300; // 5 minutes

            while (true) {
                if (connection_aborted() || (time() - $startTime) >= $maxDuration) {
                    break;
                }

                $newNotifications = Notification::where('id', '>', $lastId)
                    ->orderBy('id')
                    ->get();

                foreach ($newNotifications as $notification) {
                    $lastId = $notification->id;
                    $payload = [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'body' => $notification->body,
                        'data' => $notification->data,
                        'createdAt' => $notification->created_at?->toISOString(),
                    ];

                    echo "event: notification\n";
                    echo 'data: '.json_encode($payload)."\n\n";
                }

                if ($newNotifications->isNotEmpty()) {
                    flush();
                }

                // Heartbeat every ~15 seconds
                $heartbeatCounter++;
                if ($heartbeatCounter >= 5) {
                    echo ": heartbeat\n\n";
                    flush();
                    $heartbeatCounter = 0;
                }

                sleep(3);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Access-Control-Allow-Origin', config('app.frontend_url', 'https://enduroam.com'));

        return $response;
    }
}
