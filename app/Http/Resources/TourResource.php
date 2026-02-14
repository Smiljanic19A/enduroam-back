<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TourResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'fullDescription' => $this->full_description,
            'duration' => $this->duration,
            'difficulty' => $this->difficulty,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'location' => $this->location,
            'maxParticipants' => $this->max_participants,
            'featuredImage' => presigned_url($this->featured_image),
            'sortOrder' => $this->sort_order,
            'isActive' => $this->is_active,
            'availabilityType' => $this->availability_type,
            'availableWeekdays' => $this->available_weekdays,
            'availableDates' => $this->whenLoaded('availableDates', fn () => $this->availableDates->pluck('date')->map(fn ($d) => $d->format('Y-m-d'))),
            'includes' => IncludeItemResource::collection($this->whenLoaded('includes')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'reviews' => ReviewResource::collection($this->whenLoaded('approvedReviews')),
            'reviewCount' => $this->when(
                $this->approvedReviews !== null && $this->relationLoaded('approvedReviews'),
                fn () => $this->approvedReviews->count()
            ),
            'averageRating' => $this->when(
                $this->approvedReviews !== null && $this->relationLoaded('approvedReviews'),
                fn () => $this->approvedReviews->count() > 0
                    ? round($this->approvedReviews->avg('rating'), 1)
                    : 0
            ),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
