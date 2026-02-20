<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Banner;
use App\Services\NotificationService;
use App\Services\TranslationAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class TranslateBannerJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 120;

    public function __construct(
        private readonly int $bannerId,
    ) {}

    public function handle(TranslationAIService $service, NotificationService $notifications): void
    {
        $banner = Banner::find($this->bannerId);

        if (! $banner) {
            Log::warning("TranslateBannerJob: Banner #{$this->bannerId} not found");

            return;
        }

        $context = "Banner #{$banner->id}";

        Log::info("TranslateBannerJob: started for {$context}");

        $result = $service->translateBanner([
            'title' => $banner->title ?? '',
            'text' => $banner->text ?? '',
            'cta_text' => $banner->cta_text ?? '',
        ], $context);

        if (empty($result)) {
            $notifications->create(
                'translation_error',
                "Banner translation failed for Banner #{$banner->id}",
                null
            );

            Log::error("TranslateBannerJob: failed for {$context}");

            return;
        }

        DB::transaction(function () use ($banner, $result): void {
            foreach ($result as $locale => $trans) {
                $banner->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $trans['title'] ?? null,
                        'text' => $trans['text'] ?? null,
                        'cta_text' => $trans['cta_text'] ?? null,
                    ]
                );
            }
        });

        $notifications->create(
            'translation_complete',
            "Banner translations ready for Banner #{$banner->id}",
            null
        );

        Log::info("TranslateBannerJob: completed for {$context}", ['locales' => array_keys($result)]);
    }
}
