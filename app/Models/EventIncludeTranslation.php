<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EventIncludeTranslation extends Model
{
    protected $fillable = [
        'event_include_id',
        'locale',
        'text',
    ];

    public function eventInclude(): BelongsTo
    {
        return $this->belongsTo(EventInclude::class);
    }
}
