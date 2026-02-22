<?php

declare(strict_types=1);

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Add default payment settings
        SiteSetting::setValue('payment_paypal_link', 'https://paypal.me/Enduroam');
        SiteSetting::setValue('payment_ips_qr_image', null);
    }

    public function down(): void
    {
        SiteSetting::whereIn('key', [
            'payment_paypal_link',
            'payment_ips_qr_image',
        ])->delete();
    }
};
