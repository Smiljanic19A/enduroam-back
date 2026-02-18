<?php

declare(strict_types=1);

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'contact_email' => 'info@enduroam.com',
            'contact_phone' => '+382 69 123 456',
            'whatsapp_number' => '+382691234567',
            'address' => 'Montenegro',
            'social_instagram' => 'https://instagram.com/enduroam',
            'social_facebook' => 'https://facebook.com/enduroam',
            'social_youtube' => 'https://youtube.com/@enduroam',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public function down(): void
    {
        SiteSetting::whereIn('key', [
            'contact_email',
            'contact_phone',
            'whatsapp_number',
            'address',
            'social_instagram',
            'social_facebook',
            'social_youtube',
        ])->delete();
    }
};
