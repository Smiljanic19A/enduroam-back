<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            $table->string('text_color')->default('white')->after('text_position');
            $table->string('title_size')->default('medium')->after('text_color');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            $table->dropColumn(['text_color', 'title_size']);
        });
    }
};
