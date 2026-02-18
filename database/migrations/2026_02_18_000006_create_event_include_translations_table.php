<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_include_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_include_id')->constrained('event_includes')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('text', 255);
            $table->timestamps();

            $table->unique(['event_include_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_include_translations');
    }
};
