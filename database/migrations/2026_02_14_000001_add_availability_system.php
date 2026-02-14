<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table): void {
            $table->enum('availability_type', ['all', 'specific_dates', 'weekdays'])->default('all')->after('is_active');
            $table->json('available_weekdays')->nullable()->after('availability_type');
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->enum('availability_type', ['all', 'specific_dates', 'weekdays'])->default('specific_dates')->after('is_featured');
            $table->json('available_weekdays')->nullable()->after('availability_type');
        });

        Schema::create('available_dates', function (Blueprint $table): void {
            $table->id();
            $table->morphs('bookable');
            $table->date('date');
            $table->timestamps();

            $table->unique(['bookable_type', 'bookable_id', 'date']);
        });

        Schema::dropIfExists('tour_unavailable_dates');
    }

    public function down(): void
    {
        Schema::dropIfExists('available_dates');

        Schema::table('tours', function (Blueprint $table): void {
            $table->dropColumn(['availability_type', 'available_weekdays']);
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn(['availability_type', 'available_weekdays']);
        });

        Schema::create('tour_unavailable_dates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();

            $table->unique(['tour_id', 'date']);
        });
    }
};
