<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TourResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale', 'en');
        $translation = $this->relationLoaded('translations')
            ? $this->getTranslation($locale)
            : null;

        $isAdmin = str_starts_with($request->route()?->getName() ?? '', 'admin.');

        $data = [
            'id' => $this->id,
            'name' => $translation?->name ?? $this->name,
            'description' => $translation?->description ?? $this->description,
            'fullDescription' => $translation?->full_description ?? $this->full_description,
            'locale' => $translation ? $translation->locale : 'en',
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

        if ($isAdmin && $this->relationLoaded('translations')) {
            $data['translations'] = $this->translations
                ->groupBy('locale')
                ->map(fn ($items) => [
                    'name' => $items->first()->name,
                    'description' => $items->first()->description,
                    'fullDescription' => $items->first()->full_description,
                ]);
        }

        return $data;
    }
}
