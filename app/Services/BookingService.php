<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            $startDate = Carbon::parse($data['start_date']);

            if (! $this->isDateAvailable($bookable, $startDate)) {
                throw ValidationException::withMessages([
                    'start_date' => ['The selected date is not available for booking.'],
                ]);
            }

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

    private function isDateAvailable(Tour|Event $bookable, Carbon $date): bool
    {
        $type = $bookable->availability_type ?? 'all';

        return match ($type) {
            'all' => true,
            'specific_dates' => $bookable->availableDates()
                ->whereDate('date', $date)
                ->exists(),
            'weekdays' => is_array($bookable->available_weekdays)
                && in_array($date->dayOfWeek, $bookable->available_weekdays, true),
            default => true,
        };
    }
}
