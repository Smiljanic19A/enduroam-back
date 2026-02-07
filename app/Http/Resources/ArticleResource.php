<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->when(
                $request->routeIs('*.articles.show') || $request->routeIs('admin.*'),
                $this->content
            ),
            'image' => $this->image,
            'date' => $this->date?->format('Y-m-d'),
            'author' => $this->author,
            'isPublished' => $this->when($request->routeIs('admin.*'), $this->is_published),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
