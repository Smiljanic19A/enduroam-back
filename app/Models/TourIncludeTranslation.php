<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TourIncludeTranslation extends Model
{
    protected $fillable = [
        'tour_include_id',
        'locale',
        'text',
    ];

    public function tourInclude(): BelongsTo
    {
        return $this->belongsTo(TourInclude::class);
    }
}
