<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class GalleryImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->routeIs('admin.*');

        return [
            'id' => $this->id,
            'src' => presigned_url($this->src),
            'srcPath' => $this->when($isAdmin, $this->src),
            'alt' => $this->alt,
            'aspectRatio' => $this->aspect_ratio,
            'sortOrder' => $this->sort_order,
        ];
    }
}
