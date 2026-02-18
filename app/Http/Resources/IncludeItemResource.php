<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class IncludeItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale', 'en');
        $text = $this->text;

        if ($locale !== 'en' && $this->relationLoaded('translations')) {
            $translation = $this->translations->firstWhere('locale', $locale);
            if ($translation) {
                $text = $translation->text;
            }
        }

        $data = [
            'id' => $this->id,
            'icon' => $this->icon,
            'text' => $text,
            'sortOrder' => $this->sort_order,
        ];

        $isAdmin = str_starts_with($request->route()?->getName() ?? '', 'admin.');

        if ($isAdmin && $this->relationLoaded('translations')) {
            $data['translations'] = $this->translations
                ->groupBy('locale')
                ->map(fn ($items) => [
                    'text' => $items->first()->text,
                ]);
        }

        return $data;
    }
}
