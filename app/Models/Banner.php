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
        'title',
        'text',
        'text_position',
        'cta_text',
        'cta_href',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected function image(): Attribute
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
