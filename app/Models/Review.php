<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Review extends Model
{
    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'author',
        'rating',
        'text',
        'date',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'date' => 'date',
            'is_approved' => 'boolean',
        ];
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
