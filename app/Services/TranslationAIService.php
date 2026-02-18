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
     * @return array<string, array{name: string, description: string, full_description: ?string, includes: string[]}>
     */
    public function translate(array $content): array
    {
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

        try {
            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model', 'gpt-4o'),
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

            $result = json_decode($response->choices[0]->message->content, true);

            if (! is_array($result)) {
                Log::error('TranslationAIService: Invalid JSON response from OpenAI');

                return [];
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('TranslationAIService: OpenAI API error', [
                'error' => $e->getMessage(),
            ]);

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
