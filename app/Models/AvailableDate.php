<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class AvailableDate extends Model
{
    protected $fillable = [
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }
}
