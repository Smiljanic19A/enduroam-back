<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Tour;
use Illuminate\Support\Facades\DB;

final class BookingService
{
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data): Booking {
            $bookableType = $data['bookable_type'] === 'tour'
                ? Tour::class
                : Event::class;

            $bookable = $bookableType === Tour::class
                ? Tour::findOrFail($data['bookable_id'])
                : Event::findOrFail($data['bookable_id']);

            $totalPrice = (float) $bookable->price * $data['number_of_guests'];

            $booking = Booking::create([
                'bookable_type' => $bookableType,
                'bookable_id' => $bookable->id,
                'start_date' => $data['start_date'],
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'],
                'guest_phone' => $data['guest_phone'],
                'number_of_guests' => $data['number_of_guests'],
                'special_requests' => $data['special_requests'] ?? null,
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
                'total_price' => $totalPrice,
                'currency' => $bookable->currency,
            ]);

            if ($bookable instanceof Event && $bookable->spots_left !== null) {
                $bookable->decrement('spots_left', $data['number_of_guests']);
            }

            return $booking->load('bookable');
        });
    }

    public function updateStatus(Booking $booking, string $status): Booking
    {
        $booking->update(['status' => $status]);

        return $booking->fresh('bookable');
    }
}
