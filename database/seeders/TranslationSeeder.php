<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

final class TranslationSeeder extends Seeder
{
    private const LOCALES = ['en', 'de', 'es', 'fr', 'it', 'ru', 'sr'];

    public function run(): void
    {
        foreach (self::LOCALES as $locale) {
            $file = database_path("data/locales/{$locale}.json");

            if (! file_exists($file)) {
                $this->command->warn("Skipping {$locale}: file not found at {$file}");
                continue;
            }

            $data = json_decode(file_get_contents($file), true);

            foreach ($data as $group => $values) {
                $this->seedGroup($locale, $group, $values);
            }

            $this->command->info("Seeded {$locale} translations.");
        }
    }

    private function seedGroup(string $locale, string $group, array $values, string $prefix = ''): void
    {
        foreach ($values as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $this->seedGroup($locale, $group, $value, $fullKey);
            } else {
                Translation::firstOrCreate(
                    [
                        'locale' => $locale,
                        'group' => $group,
                        'key' => $fullKey,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }
    }
}
