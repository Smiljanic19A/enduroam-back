<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\BookingConfirmation;
use App\Mail\BookingStatusUpdate;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

final class BookingService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data): Booking {
            $bookableType = $data['bookable_type'] === 'tour'
                ? Tour::class
                : Event::class;

            $bookable = $bookableType === Tour::class
                ? Tour::findOrFail($data['bookable_id'])
                : Event::findOrFail($data['bookable_id']);

            // S3-05: Reject bookings for advertisement items
            if ($bookable->is_advertisement) {
                throw ValidationException::withMessages([
                    'bookable_id' => ['This item is for display only and cannot be booked.'],
                ]);
            }

            $startDate = Carbon::parse($data['start_date']);

            // S3-06: Calculate end date for multi-day tours
            $endDate = null;
            if ($bookable instanceof Tour && $bookable->duration > 1) {
                if ($bookable->is_flexible_duration && ! empty($data['end_date'])) {
                    $endDate = Carbon::parse($data['end_date']);
                } else {
                    $endDate = $startDate->copy()->addDays($bookable->duration - 1);
                }
            }

            // Validate entire date range (or single date)
            if ($endDate) {
                $current = $startDate->copy();
                while ($current->lte($endDate)) {
                    if (! $this->isDateAvailable($bookable, $current)) {
                        throw ValidationException::withMessages([
                            'start_date' => ["The date {$current->format('Y-m-d')} is not available for booking."],
                        ]);
                    }
                    $current->addDay();
                }
            } else {
                if (! $this->isDateAvailable($bookable, $startDate)) {
                    throw ValidationException::withMessages([
                        'start_date' => ['The selected date is not available for booking.'],
                    ]);
                }
            }

            // S3-04: Inquiry pricing — null price for inquiry tours
            $isInquiry = $bookable instanceof Tour && $bookable->is_inquiry_price;

            $totalPrice = $isInquiry ? null : (float) $bookable->price * $data['number_of_guests'];
            $depositPercentage = $bookable->deposit_percentage ?? 100;
            $depositAmount = $isInquiry ? null : round($totalPrice * ($depositPercentage / 100), 2);

            $booking = Booking::create([
                'bookable_type' => $bookableType,
                'bookable_id' => $bookable->id,
                'start_date' => $data['start_date'],
                'end_date' => $endDate?->format('Y-m-d'),
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'],
                'guest_phone' => $data['guest_phone'],
                'number_of_guests' => $data['number_of_guests'],
                'special_requests' => $data['special_requests'] ?? null,
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
                'total_price' => $totalPrice,
                'deposit_amount' => $depositAmount,
                'currency' => $bookable->currency,
            ]);

            if ($bookable instanceof Event && $bookable->spots_left !== null) {
                $bookable->decrement('spots_left', $data['number_of_guests']);
            }

            $notificationType = $isInquiry ? 'new_inquiry' : 'new_booking';
            $notificationTitle = $isInquiry
                ? "New inquiry from {$booking->guest_name}"
                : "New booking from {$booking->guest_name}";

            $this->notificationService->create(
                type: $notificationType,
                title: $notificationTitle,
                body: "{$booking->number_of_guests} guest(s) for {$bookable->name}",
                data: [
                    'booking_id' => $booking->id,
                    'bookable_type' => $data['bookable_type'],
                    'bookable_name' => $bookable->name,
                    'guest_name' => $booking->guest_name,
                    'total_price' => $isInquiry ? null : (float) $booking->total_price,
                    'currency' => $booking->currency,
                ],
            );

            Mail::to($booking->guest_email)
                ->send(new BookingConfirmation($booking));

            return $booking->load('bookable');
        });
    }

    public function updateStatus(Booking $booking, string $status): Booking
    {
        $booking->update(['status' => $status]);

        $booking = $booking->fresh('bookable');

        Mail::to($booking->guest_email)
            ->send(new BookingStatusUpdate($booking));

        return $booking;
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
                && in_array($date->dayOfWeek, array_map('intval', $bookable->available_weekdays)),
            default => true,
        };
    }
}
