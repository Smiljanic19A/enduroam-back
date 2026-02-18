<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            $table->string('mobile_image', 2048)->nullable()->after('image');
            $table->string('title_color', 20)->nullable()->default('#FFFFFF')->after('text_color');
        });

        // Convert existing text_color values from words to hex
        DB::table('banners')->where('text_color', 'white')->update(['text_color' => '#FFFFFF']);
        DB::table('banners')->where('text_color', 'black')->update(['text_color' => '#000000']);
    }

    public function down(): void
    {
        // Convert hex back to words
        DB::table('banners')->where('text_color', '#FFFFFF')->update(['text_color' => 'white']);
        DB::table('banners')->where('text_color', '#000000')->update(['text_color' => 'black']);

        Schema::table('banners', function (Blueprint $table): void {
            $table->dropColumn(['mobile_image', 'title_color']);
        });
    }
};
