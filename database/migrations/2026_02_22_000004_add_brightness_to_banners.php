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
            $table->unsignedSmallInteger('brightness')->default(100)->after('overlay_opacity');
        });

        // Change overlay_opacity default from 50 to 0 (natural = no overlay)
        Schema::table('banners', function (Blueprint $table): void {
            $table->unsignedTinyInteger('overlay_opacity')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            $table->dropColumn('brightness');
        });

        Schema::table('banners', function (Blueprint $table): void {
            $table->unsignedTinyInteger('overlay_opacity')->default(50)->change();
        });
    }
};
