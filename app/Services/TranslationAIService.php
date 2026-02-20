<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

final class TranslationAIService
{
    private const TARGET_LOCALES = ['de', 'es', 'fr', 'it', 'ru', 'sr'];

    /**
     * Translate tour/event content from English to all target locales.
     *
     * @param  array{name: string, description: string, full_description: ?string, includes: string[]}  $content
     * @param  string  $context  Identifier for logs (e.g. "Tour #3 (Mountain Explorer)")
     * @return array<string, array{name: string, description: string, full_description: ?string, includes: string[]}>
     */
    public function translate(array $content, string $context = ''): array
    {
        $logPrefix = $context ? "[{$context}]" : '[unknown]';

        Log::info("Translation {$logPrefix}: preparing request", [
            'name' => $content['name'],
            'description_length' => strlen($content['description']),
            'full_description_length' => strlen($content['full_description'] ?? ''),
            'includes_count' => count($content['includes'] ?? []),
            'target_locales' => self::TARGET_LOCALES,
        ]);

        $includesText = '';
        if (! empty($content['includes'])) {
            $includesList = implode("\n", array_map(
                fn (string $text, int $i) => ($i + 1).'. '.$text,
                $content['includes'],
                array_keys($content['includes'])
            ));
            $includesText = "\n\n## Includes items (translate each line):\n{$includesList}";
        }

        $localeNames = [
            'de' => 'German',
            'es' => 'Spanish',
            'fr' => 'French',
            'it' => 'Italian',
            'ru' => 'Russian',
            'sr' => 'Serbian',
        ];

        $localeList = implode(', ', array_map(
            fn (string $code, string $name) => "{$code} ({$name})",
            array_keys($localeNames),
            $localeNames
        ));

        $systemPrompt = "You are a professional translator for an enduro motorcycle tourism website. Translate content accurately, preserving HTML formatting tags (like <p>, <strong>, <em>, <ul>, <li>, <h2>, <h3>, <a>) and maintaining the adventurous, professional tone. Do not translate brand names or proper nouns.";

        $userPrompt = <<<PROMPT
Translate the following English content into these languages: {$localeList}

## Name:
{$content['name']}

## Description:
{$content['description']}

## Full Description:
{$content['full_description']}{$includesText}

Respond with a JSON object where each key is a locale code, containing: name, description, full_description, and includes (array of translated include texts in the same order).
PROMPT;

        $schema = $this->buildJsonSchema(count($content['includes'] ?? []));
        $model = config('services.openai.model', 'gpt-4o');

        Log::info("Translation {$logPrefix}: sending to OpenAI", [
            'model' => $model,
            'prompt_length' => strlen($userPrompt),
        ]);

        $startTime = microtime(true);

        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'translations',
                        'strict' => true,
                        'schema' => $schema,
                    ],
                ],
                'temperature' => 0.3,
            ]);

            $elapsed = round(microtime(true) - $startTime, 2);

            Log::info("Translation {$logPrefix}: response received from OpenAI", [
                'elapsed_seconds' => $elapsed,
                'usage' => [
                    'prompt_tokens' => $response->usage->promptTokens ?? null,
                    'completion_tokens' => $response->usage->completionTokens ?? null,
                    'total_tokens' => $response->usage->totalTokens ?? null,
                ],
                'finish_reason' => $response->choices[0]->finishReason ?? null,
            ]);

            $result = json_decode($response->choices[0]->message->content, true);

            if (! is_array($result)) {
                Log::error("Translation {$logPrefix}: invalid JSON in OpenAI response", [
                    'raw_content' => mb_substr($response->choices[0]->message->content, 0, 500),
                ]);

                return [];
            }

            $localesReceived = array_keys($result);
            Log::info("Translation {$logPrefix}: parsed successfully", [
                'locales_received' => $localesReceived,
            ]);

            return $result;
        } catch (\Throwable $e) {
            $elapsed = round(microtime(true) - $startTime, 2);

            Log::error("Translation {$logPrefix}: OpenAI API error", [
                'error' => $e->getMessage(),
                'elapsed_seconds' => $elapsed,
                'exception_class' => get_class($e),
            ]);

            return [];
        }
    }

    /**
     * Translate banner content (title, text, cta_text) from English to all target locales.
     *
     * @param  array{title: ?string, text: ?string, cta_text: ?string}  $content
     * @param  string  $context
     * @return array<string, array{title: string, text: string, cta_text: string}>
     */
    public function translateBanner(array $content, string $context = ''): array
    {
        $logPrefix = $context ? "[{$context}]" : '[unknown]';

        $localeNames = [
            'de' => 'German', 'es' => 'Spanish', 'fr' => 'French',
            'it' => 'Italian', 'ru' => 'Russian', 'sr' => 'Serbian',
        ];

        $localeList = implode(', ', array_map(
            fn (string $code, string $name) => "{$code} ({$name})",
            array_keys($localeNames),
            $localeNames
        ));

        $systemPrompt = "You are a professional translator for an enduro motorcycle tourism website. Translate content accurately, preserving HTML formatting tags (like <p>, <strong>, <em>, <ul>, <li>, <h2>, <h3>, <a>) and maintaining the adventurous, professional tone. Do not translate brand names or proper nouns.";

        $userPrompt = <<<PROMPT
Translate the following English banner content into these languages: {$localeList}

## Title:
{$content['title']}

## Subtitle/Text (may contain HTML):
{$content['text']}

## CTA Button Text:
{$content['cta_text']}

Respond with a JSON object where each key is a locale code, containing: title, text, cta_text.
PROMPT;

        $localeSchema = [
            'type' => 'object',
            'properties' => [
                'title' => ['type' => 'string'],
                'text' => ['type' => 'string'],
                'cta_text' => ['type' => 'string'],
            ],
            'required' => ['title', 'text', 'cta_text'],
            'additionalProperties' => false,
        ];

        $properties = [];
        $required = [];
        foreach (self::TARGET_LOCALES as $locale) {
            $properties[$locale] = $localeSchema;
            $required[] = $locale;
        }

        $schema = [
            'type' => 'object',
            'properties' => $properties,
            'required' => $required,
            'additionalProperties' => false,
        ];

        $model = config('services.openai.model', 'gpt-4o');

        Log::info("Banner translation {$logPrefix}: sending to OpenAI", ['model' => $model]);

        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'banner_translations',
                        'strict' => true,
                        'schema' => $schema,
                    ],
                ],
                'temperature' => 0.3,
            ]);

            $result = json_decode($response->choices[0]->message->content, true);

            if (! is_array($result)) {
                Log::error("Banner translation {$logPrefix}: invalid JSON response");

                return [];
            }

            Log::info("Banner translation {$logPrefix}: success", ['locales' => array_keys($result)]);

            return $result;
        } catch (\Throwable $e) {
            Log::error("Banner translation {$logPrefix}: OpenAI error", ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Translate UI strings for a single target locale.
     *
     * @param  array<string, string>  $strings  Flat map of 'group.key' => 'English value'
     * @param  string  $targetLocale
     * @return array<string, string>  Flat map of 'group.key' => 'translated value'
     */
    public function translateUIStrings(array $strings, string $targetLocale): array
    {
        if (empty($strings)) {
            return [];
        }

        $localeNames = [
            'de' => 'German', 'es' => 'Spanish', 'fr' => 'French',
            'it' => 'Italian', 'ru' => 'Russian', 'sr' => 'Serbian',
        ];

        $targetName = $localeNames[$targetLocale] ?? $targetLocale;

        $systemPrompt = "You are a professional translator for an enduro motorcycle tourism website. Translate UI strings from English to {$targetName}. Keep translations concise and natural for a website UI. Preserve any {placeholder} variables exactly as-is (e.g. {year}, {count}). Do not translate brand names.";

        $stringsList = json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $userPrompt = <<<PROMPT
Translate the following English UI strings to {$targetName}.

The input is a JSON object where keys are "group.key" identifiers and values are English strings.
Return a JSON object with the same keys and translated values.
Preserve any {placeholder} variables exactly as they appear.

{$stringsList}
PROMPT;

        $keyList = array_keys($strings);
        $properties = [];
        foreach ($keyList as $key) {
            $properties[$key] = ['type' => 'string'];
        }

        $schema = [
            'type' => 'object',
            'properties' => $properties,
            'required' => $keyList,
            'additionalProperties' => false,
        ];

        $model = config('services.openai.model', 'gpt-4o');

        Log::info("UI string translation: sending batch to OpenAI", [
            'locale' => $targetLocale,
            'count' => count($strings),
            'model' => $model,
        ]);

        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'ui_translations',
                        'strict' => true,
                        'schema' => $schema,
                    ],
                ],
                'temperature' => 0.2,
            ]);

            $result = json_decode($response->choices[0]->message->content, true);

            if (! is_array($result)) {
                Log::error("UI string translation: invalid JSON for locale {$targetLocale}");

                return [];
            }

            Log::info("UI string translation: success", [
                'locale' => $targetLocale,
                'translated' => count($result),
            ]);

            return $result;
        } catch (\Throwable $e) {
            Log::error("UI string translation: OpenAI error for locale {$targetLocale}", ['error' => $e->getMessage()]);

            return [];
        }
    }

    private function buildJsonSchema(int $includesCount): array
    {
        $localeSchema = [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'full_description' => ['type' => 'string'],
                'includes' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
            ],
            'required' => ['name', 'description', 'full_description', 'includes'],
            'additionalProperties' => false,
        ];

        $properties = [];
        $required = [];

        foreach (self::TARGET_LOCALES as $locale) {
            $properties[$locale] = $localeSchema;
            $required[] = $locale;
        }

        return [
            'type' => 'object',
            'properties' => $properties,
            'required' => $required,
            'additionalProperties' => false,
        ];
    }
}
