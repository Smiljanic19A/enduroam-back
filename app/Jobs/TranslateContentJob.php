<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\NotificationService;
use App\Services\TranslationAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class TranslateContentJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 300;

    /**
     * @param  class-string  $modelClass  Tour::class or Event::class
     * @param  int[]|null  $modelIds  Specific IDs to translate, or null for all
     */
    public function __construct(
        private readonly string $modelClass,
        private readonly ?array $modelIds = null,
    ) {}

    public function handle(TranslationAIService $service, NotificationService $notifications): void
    {
        $type = class_basename($this->modelClass);
        $isBulk = $this->modelIds === null;

        Log::info("TranslateContentJob: started", [
            'type' => $type,
            'mode' => $isBulk ? 'bulk' : 'single',
            'model_ids' => $this->modelIds,
        ]);

        $query = $this->modelClass::with('includes');

        if ($this->modelIds !== null) {
            $query->whereIn('id', $this->modelIds);
        }

        $items = $query->get();

        Log::info("TranslateContentJob: loaded {$items->count()} {$type}(s) from DB");

        $translated = 0;
        $failed = 0;
        $errors = [];

        foreach ($items as $index => $item) {
            $context = "{$type} #{$item->id} ({$item->name})";
            $position = ($index + 1) . '/' . $items->count();

            Log::info("TranslateContentJob: [{$position}] translating {$context}");

            $result = $service->translate([
                'name' => $item->name,
                'description' => $item->description,
                'full_description' => $item->full_description ?? '',
                'includes' => $item->includes->pluck('text')->toArray(),
            ], $context);

            if (empty($result)) {
                $failed++;
                $errors[] = $context;
                Log::warning("TranslateContentJob: [{$position}] FAILED {$context}");

                continue;
            }

            Log::info("TranslateContentJob: [{$position}] saving translations for {$context}", [
                'locales' => array_keys($result),
            ]);

            DB::transaction(function () use ($item, $result): void {
                foreach ($result as $locale => $trans) {
                    $item->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $trans['name'],
                            'description' => $trans['description'],
                            'full_description' => $trans['full_description'] ?? null,
                        ]
                    );

                    if (! empty($trans['includes'])) {
                        foreach ($item->includes as $idx => $include) {
                            if (isset($trans['includes'][$idx])) {
                                $include->translations()->updateOrCreate(
                                    ['locale' => $locale],
                                    ['text' => $trans['includes'][$idx]]
                                );
                            }
                        }
                    }
                }
            });

            Log::info("TranslateContentJob: [{$position}] saved {$context}");
            $translated++;
        }

        $isSingle = count($items) === 1;

        if ($failed > 0 && $translated === 0) {
            $title = $isSingle
                ? "Translation failed for {$type}: {$items->first()->name}"
                : "{$type} translation failed — {$failed} items could not be translated";

            $notification = $notifications->create('translation_error', $title, implode(', ', $errors));
            Log::error("TranslateContentJob: finished with ALL FAILURES", [
                'type' => $type,
                'failed' => $failed,
                'errors' => $errors,
                'notification_id' => $notification->id,
            ]);
        } else {
            $title = $isSingle
                ? "Translations ready for {$type}: {$items->first()->name}"
                : "{$type} translations complete — {$translated} done" . ($failed > 0 ? ", {$failed} failed" : '');

            $body = $failed > 0 ? 'Failed: ' . implode(', ', $errors) : null;
            $notification = $notifications->create('translation_complete', $title, $body);
            Log::info("TranslateContentJob: finished", [
                'type' => $type,
                'translated' => $translated,
                'failed' => $failed,
                'notification_id' => $notification->id,
            ]);
        }
    }
}
