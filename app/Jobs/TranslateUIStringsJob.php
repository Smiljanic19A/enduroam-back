<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Translation;
use App\Services\NotificationService;
use App\Services\TranslationAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class TranslateUIStringsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 600;

    private const TARGET_LOCALES = ['de', 'es', 'fr', 'it', 'ru', 'sr'];

    public function handle(TranslationAIService $service, NotificationService $notifications): void
    {
        Log::info('TranslateUIStringsJob: started');

        // Load all English source strings
        $englishStrings = Translation::forLocale('en')
            ->get()
            ->keyBy(fn ($t) => "{$t->group}.{$t->key}");

        if ($englishStrings->isEmpty()) {
            Log::warning('TranslateUIStringsJob: no English strings found');
            $notifications->create('translation_error', 'UI auto-translate failed: no English source strings found', null);

            return;
        }

        $affectedLocales = [];
        $totalTranslated = 0;
        $totalFailed = 0;

        foreach (self::TARGET_LOCALES as $locale) {
            // Find keys that already have a non-empty translation for this locale
            $existingDotKeys = Translation::forLocale($locale)
                ->whereNotNull('value')
                ->where('value', '!=', '')
                ->get(['group', 'key'])
                ->map(fn ($t) => "{$t->group}.{$t->key}")
                ->flip()
                ->all();

            // Build flat map of missing strings: 'group.key' => 'English value'
            $missing = [];
            foreach ($englishStrings as $dotKey => $translation) {
                if (! isset($existingDotKeys[$dotKey])) {
                    $missing[$dotKey] = $translation->value ?? '';
                }
            }

            if (empty($missing)) {
                Log::info("TranslateUIStringsJob: locale {$locale} is complete, skipping");

                continue;
            }

            Log::info("TranslateUIStringsJob: translating {$locale}", ['missing_count' => count($missing)]);

            // Split into batches of 50 keys to avoid token limits
            $batches = array_chunk($missing, 50, true);
            $localeTranslated = 0;
            $localeFailed = 0;

            foreach ($batches as $batchIndex => $batch) {
                $result = $service->translateUIStrings($batch, $locale);

                if (empty($result)) {
                    Log::warning("TranslateUIStringsJob: batch {$batchIndex} failed for {$locale}");
                    $localeFailed += count($batch);

                    continue;
                }

                foreach ($result as $dotKey => $value) {
                    [$group, $key] = array_pad(explode('.', $dotKey, 2), 2, '');

                    if (! $group || ! $key) {
                        continue;
                    }

                    Translation::updateOrCreate(
                        ['locale' => $locale, 'group' => $group, 'key' => $key],
                        ['value' => $value]
                    );

                    $localeTranslated++;
                }
            }

            if ($localeTranslated > 0) {
                $affectedLocales[] = $locale;
                $this->publishLocale($locale);
            }

            $totalTranslated += $localeTranslated;
            $totalFailed += $localeFailed;

            Log::info("TranslateUIStringsJob: {$locale} done", [
                'translated' => $localeTranslated,
                'failed' => $localeFailed,
            ]);
        }

        $message = "UI translations complete — {$totalTranslated} strings translated across " . count($affectedLocales) . ' locales';

        if ($totalFailed > 0) {
            $message .= ", {$totalFailed} failed";
        }

        $notifications->create('translation_complete', $message, null);

        Log::info('TranslateUIStringsJob: finished', [
            'total_translated' => $totalTranslated,
            'total_failed' => $totalFailed,
            'affected_locales' => $affectedLocales,
        ]);
    }

    private function publishLocale(string $locale): void
    {
        $json = Translation::buildLocaleJson($locale);

        Storage::disk('s3-translations')->put(
            "{$locale}.json",
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'public'
        );
    }
}
