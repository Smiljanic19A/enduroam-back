<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

final class SettingController extends Controller
{
    /**
     * Keys exposed publicly (contact info + socials).
     */
    private const PUBLIC_KEYS = [
        'contact_email',
        'contact_phone',
        'whatsapp_number',
        'address',
        'social_instagram',
        'social_facebook',
        'social_youtube',
    ];

    public function contact(): JsonResponse
    {
        $settings = SiteSetting::whereIn('key', self::PUBLIC_KEYS)
            ->pluck('value', 'key');

        $result = [];
        foreach (self::PUBLIC_KEYS as $key) {
            $result[$key] = $settings[$key] ?? null;
        }

        return response()->json(['data' => $result]);
    }
}
