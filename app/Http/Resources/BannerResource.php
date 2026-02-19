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

        return [
            'id' => $this->id,
            'type' => $this->type,
            'image' => presigned_url($this->image),
            'imagePath' => $this->when($isAdmin, $this->image),
            'mobileImage' => presigned_url($this->mobile_image),
            'mobileImagePath' => $this->when($isAdmin, $this->mobile_image),
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
            'sortOrder' => $this->sort_order,
            'isActive' => $this->when($isAdmin, $this->is_active),
        ];
    }
}
