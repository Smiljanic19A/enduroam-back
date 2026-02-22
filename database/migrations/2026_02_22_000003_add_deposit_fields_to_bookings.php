<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('total_price');
            $table->timestamp('payment_email_sent_at')->nullable()->after('deposit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['deposit_amount', 'payment_email_sent_at']);
        });
    }
};
