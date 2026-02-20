<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->routeIs('admin.*');

        if ($isAdmin) {
            return $this->toAdminArray();
        }

        return $this->toPublicArray($request);
    }

    private function toAdminArray(): array
    {
        // Build translations map keyed by locale
        $translationsMap = [];
        if ($this->relationLoaded('translations')) {
            foreach ($this->translations as $t) {
                $translationsMap[$t->locale] = [
                    'title' => $t->title,
                    'text' => $t->text,
                    'cta_text' => $t->cta_text,
                ];
            }
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'image' => presigned_url($this->image),
            'imagePath' => $this->image,
            'mobileImage' => presigned_url($this->mobile_image),
            'mobileImagePath' => $this->mobile_image,
            'title' => $this->title,
            'text' => $this->text,
            'textPosition' => $this->text_position,
            'textColor' => $this->text_color,
            'titleColor' => $this->title_color,
            'titleSize' => $this->title_size,
            'focalPoint' => $this->focal_point,
            'overlayOpacity' => $this->overlay_opacity,
            'imageFit' => $this->image_fit,
            'animation' => $this->animation,
            'cta' => $this->cta_text ? [
                'text' => $this->cta_text,
                'href' => $this->cta_href,
            ] : null,
            'ctaText' => $this->cta_text,
            'ctaHref' => $this->cta_href,
            'sortOrder' => $this->sort_order,
            'isActive' => $this->is_active,
            'translations' => $translationsMap,
        ];
    }

    private function toPublicArray(Request $request): array
    {
        // Resolve locale from ?locale= param, Accept-Language header, or fall back to 'en'
        $locale = $request->query('locale')
            ?? $this->parseAcceptLanguage($request->header('Accept-Language', 'en'))
            ?? 'en';

        $title = $this->title;
        $text = $this->text;
        $ctaText = $this->cta_text;

        if ($locale !== 'en' && $this->relationLoaded('translations')) {
            $translation = $this->translations->firstWhere('locale', $locale);

            if ($translation) {
                $title = $translation->title ?: $this->title;
                $text = $translation->text ?: $this->text;
                $ctaText = $translation->cta_text ?: $this->cta_text;
            }
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'image' => presigned_url($this->image),
            'mobileImage' => presigned_url($this->mobile_image),
            'title' => $title,
            'text' => $text,
            'textPosition' => $this->text_position,
            'textColor' => $this->text_color,
            'titleColor' => $this->title_color,
            'titleSize' => $this->title_size,
            'focalPoint' => $this->focal_point,
            'overlayOpacity' => $this->overlay_opacity,
            'imageFit' => $this->image_fit,
            'animation' => $this->animation,
            'cta' => $ctaText ? [
                'text' => $ctaText,
                'href' => $this->cta_href,
            ] : null,
            'sortOrder' => $this->sort_order,
        ];
    }

    private function parseAcceptLanguage(string $header): ?string
    {
        // Extract the primary language tag (e.g. "de-DE,de;q=0.9" → "de")
        if (preg_match('/^([a-z]{2})/i', $header, $m)) {
            return strtolower($m[1]);
        }

        return null;
    }
}
