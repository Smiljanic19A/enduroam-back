<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ContactMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'isRead' => $this->is_read,
            'replyMessage' => $this->reply_message,
            'repliedAt' => $this->replied_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
        ];
    }
}
