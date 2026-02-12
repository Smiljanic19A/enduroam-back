<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Translation extends Model
{
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    public function scopeForGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Build nested JSON structure for a locale (same shape as the frontend JSON files).
     */
    public static function buildLocaleJson(string $locale): array
    {
        $translations = static::forLocale($locale)
            ->orderBy('group')
            ->orderBy('key')
            ->get();

        $result = [];

        foreach ($translations as $translation) {
            $group = $translation->group;
            $key = $translation->key;

            if (! isset($result[$group])) {
                $result[$group] = [];
            }

            // Support nested keys like "difficulty.easy" → { "difficulty": { "easy": "..." } }
            $parts = explode('.', $key);
            $target = &$result[$group];

            foreach (array_slice($parts, 0, -1) as $part) {
                if (! isset($target[$part]) || ! is_array($target[$part])) {
                    $target[$part] = [];
                }
                $target = &$target[$part];
            }

            $target[end($parts)] = $translation->value ?? '';
            unset($target);
        }

        return $result;
    }
}
