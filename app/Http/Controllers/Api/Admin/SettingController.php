<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SettingController extends Controller
{
    /**
     * Allowed setting keys that can be managed via the admin panel.
     */
    private const ALLOWED_KEYS = [
        'contact_email',
        'contact_phone',
        'whatsapp_number',
        'address',
        'social_instagram',
        'social_facebook',
        'social_youtube',
    ];

    public function index(): JsonResponse
    {
        $settings = SiteSetting::whereIn('key', self::ALLOWED_KEYS)
            ->pluck('value', 'key');

        // Ensure all keys are present (null for unset)
        $result = [];
        foreach (self::ALLOWED_KEYS as $key) {
            $result[$key] = $settings[$key] ?? null;
        }

        return response()->json(['data' => $result]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'contact_phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'whatsapp_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'social_instagram' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'social_facebook' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'social_youtube' => ['sometimes', 'nullable', 'url', 'max:2048'],
        ]);

        foreach ($data as $key => $value) {
            if (in_array($key, self::ALLOWED_KEYS, true)) {
                SiteSetting::setValue($key, $value);
            }
        }

        // Return the full updated state
        $settings = SiteSetting::whereIn('key', self::ALLOWED_KEYS)
            ->pluck('value', 'key');

        $result = [];
        foreach (self::ALLOWED_KEYS as $key) {
            $result[$key] = $settings[$key] ?? null;
        }

        return response()->json([
            'message' => 'Settings updated successfully.',
            'data' => $result,
        ]);
    }
}
