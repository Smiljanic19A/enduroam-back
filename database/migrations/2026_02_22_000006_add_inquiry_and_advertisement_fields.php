<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->boolean('is_inquiry_price')->default(false)->after('deposit_percentage');
            $table->boolean('is_advertisement')->default(false)->after('is_inquiry_price');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_advertisement')->default(false)->after('deposit_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['is_inquiry_price', 'is_advertisement']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_advertisement');
        });
    }
};
