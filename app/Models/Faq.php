<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(FaqTranslation::class);
    }

    public function getTranslation(string $locale): ?FaqTranslation
    {
        if ($locale === 'en') {
            return null;
        }

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en');
    }
}
