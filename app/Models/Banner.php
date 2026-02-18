<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Banner extends Model
{
    protected $fillable = [
        'type',
        'image',
        'mobile_image',
        'title',
        'text',
        'text_position',
        'text_color',
        'title_color',
        'title_size',
        'focal_point',
        'overlay_opacity',
        'image_fit',
        'animation',
        'cta_text',
        'cta_href',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'overlay_opacity' => 'integer',
            'is_active' => 'boolean',
            'animation' => 'array',
        ];
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => to_s3_path($value),
        );
    }

    protected function mobileImage(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => to_s3_path($value),
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
