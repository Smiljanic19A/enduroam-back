<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->longText('full_description')->nullable();
            $table->date('date');
            $table->unsignedInteger('duration');
            $table->enum('difficulty', ['easy', 'intermediate', 'advanced', 'expert']);
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('€');
            $table->string('location');
            $table->unsignedInteger('max_participants');
            $table->unsignedInteger('spots_left')->nullable();
            $table->string('featured_image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
