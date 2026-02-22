<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->boolean('automatic_payment')->default(true)->after('is_active');
            $table->unsignedTinyInteger('deposit_percentage')->default(20)->after('automatic_payment');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->boolean('automatic_payment')->default(true)->after('is_active');
            $table->unsignedTinyInteger('deposit_percentage')->default(100)->after('automatic_payment');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['automatic_payment', 'deposit_percentage']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['automatic_payment', 'deposit_percentage']);
        });
    }
};
