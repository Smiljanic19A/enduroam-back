<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class FaqResource extends JsonResource
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
            'question' => $translation?->question ?? $this->question,
            'answer' => $translation?->answer ?? $this->answer,
            'locale' => $translation ? $translation->locale : 'en',
            'sortOrder' => $this->sort_order,
        ];

        if ($isAdmin && $this->relationLoaded('translations')) {
            $data['translations'] = $this->translations
                ->groupBy('locale')
                ->map(fn ($items) => [
                    'question' => $items->first()->question,
                    'answer' => $items->first()->answer,
                ]);
        }

        return $data;
    }
}
