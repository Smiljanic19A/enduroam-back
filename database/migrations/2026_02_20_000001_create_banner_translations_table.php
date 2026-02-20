<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banner_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('banner_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title', 255)->nullable();
            $table->text('text')->nullable();
            $table->string('cta_text', 255)->nullable();
            $table->timestamps();

            $table->unique(['banner_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_translations');
    }
};
