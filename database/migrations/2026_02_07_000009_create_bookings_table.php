<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->morphs('bookable');
            $table->date('start_date');
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->unsignedInteger('number_of_guests')->default(1);
            $table->text('special_requests')->nullable();
            $table->string('payment_method')->default('stripe');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 10)->default('€');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status']);
            $table->index(['bookable_type', 'bookable_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
