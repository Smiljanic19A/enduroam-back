<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'image' => $this->image,
            'title' => $this->title,
            'text' => $this->text,
            'textPosition' => $this->text_position,
            'cta' => $this->cta_text ? [
                'text' => $this->cta_text,
                'href' => $this->cta_href,
            ] : null,
            'sortOrder' => $this->sort_order,
            'isActive' => $this->when($request->routeIs('admin.*'), $this->is_active),
        ];
    }
}
