<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author,
            'rating' => $this->rating,
            'text' => $this->text,
            'date' => $this->date?->format('Y-m-d'),
            'isApproved' => $this->is_approved,
            'reviewableType' => $this->when($request->routeIs('admin.*'), $this->reviewable_type),
            'reviewableId' => $this->when($request->routeIs('admin.*'), $this->reviewable_id),
        ];
    }
}
