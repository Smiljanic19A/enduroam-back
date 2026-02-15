<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

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
}
