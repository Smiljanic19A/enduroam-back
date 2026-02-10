<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EventImage extends Model
{
    protected $fillable = [
        'event_id',
        'path',
        'alt',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected function path(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => to_s3_path($value),
        );
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
