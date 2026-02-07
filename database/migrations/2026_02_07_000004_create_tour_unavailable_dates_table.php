<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_unavailable_dates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();

            $table->unique(['tour_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_unavailable_dates');
    }
};
