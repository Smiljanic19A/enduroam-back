<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SponsorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->routeIs('admin.*');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => presigned_url($this->logo),
            'logoPath' => $this->when($isAdmin, $this->logo),
            'url' => $this->url,
            'sortOrder' => $this->sort_order,
        ];
    }
}
