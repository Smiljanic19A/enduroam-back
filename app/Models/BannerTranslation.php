<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BannerTranslation extends Model
{
    protected $fillable = [
        'banner_id',
        'locale',
        'title',
        'text',
        'cta_text',
    ];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }
}
