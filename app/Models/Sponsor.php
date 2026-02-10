<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected function logo(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => to_s3_path($value),
        );
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
