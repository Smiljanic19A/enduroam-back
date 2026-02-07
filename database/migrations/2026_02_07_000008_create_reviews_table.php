<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->morphs('reviewable');
            $table->string('author');
            $table->unsignedTinyInteger('rating');
            $table->text('text');
            $table->date('date');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
