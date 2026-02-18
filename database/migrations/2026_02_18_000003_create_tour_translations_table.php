<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name', 255);
            $table->text('description');
            $table->longText('full_description')->nullable();
            $table->timestamps();

            $table->unique(['tour_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_translations');
    }
};
