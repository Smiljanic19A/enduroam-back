<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\TranslateUIStringsJob;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class TranslationController extends Controller
{
    /**
     * List translations with optional locale/group/search filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Translation::query()
            ->orderBy('group')
            ->orderBy('key');

        if ($request->filled('locale')) {
            $query->forLocale($request->input('locale'));
        }

        if ($request->filled('group')) {
            $query->forGroup($request->input('group'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    /**
     * Get all available groups (sections).
     */
    public function groups(): JsonResponse
    {
        $groups = Translation::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return response()->json(['data' => $groups]);
    }

    /**
     * Bulk update translations and publish to Wasabi.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'translations' => ['required', 'array'],
            'translations.*.id' => ['required', 'integer', 'exists:translations,id'],
            'translations.*.value' => ['required', 'string'],
        ]);

        $affectedLocales = [];

        foreach ($request->input('translations') as $item) {
            $translation = Translation::find($item['id']);
            $translation->update(['value' => $item['value']]);
            $affectedLocales[$translation->locale] = true;
        }

        // Publish affected locales to Wasabi
        foreach (array_keys($affectedLocales) as $locale) {
            $this->publishLocale($locale);
        }

        return response()->json(['message' => 'Translations updated and published.']);
    }

    /**
     * Publish all locales to Wasabi.
     */
    public function publishAll(): JsonResponse
    {
        $locales = Translation::query()
            ->select('locale')
            ->distinct()
            ->pluck('locale');

        foreach ($locales as $locale) {
            $this->publishLocale($locale);
        }

        return response()->json([
            'message' => 'All translations published.',
            'locales' => $locales,
        ]);
    }

    /**
     * Dispatch background job to auto-translate all missing UI strings.
     */
    public function autoTranslate(): JsonResponse
    {
        TranslateUIStringsJob::dispatch();

        return response()->json(['message' => 'Auto-translation queued. You will be notified when complete.'], 202);
    }

    /**
     * Build the JSON for a locale and upload to Wasabi as a public file.
     */
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
