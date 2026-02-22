<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Mail\BookingConfirmation;
use App\Mail\PaymentLinkEmail;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Mail;

final class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Booking::with('bookable')->latest();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('bookable_type')) {
            $type = $request->input('bookable_type') === 'tour'
                ? 'App\\Models\\Tour'
                : 'App\\Models\\Event';
            $query->where('bookable_type', $type);
        }

        $bookings = $query->paginate($request->input('per_page', 20));

        return BookingResource::collection($bookings);
    }

    public function show(Booking $booking): BookingResource
    {
        $booking->load('bookable');

        return new BookingResource($booking);
    }

    public function updateStatus(Request $request, Booking $booking): BookingResource
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
        ]);

        $booking = $this->bookingService->updateStatus($booking, $data['status']);

        return new BookingResource($booking);
    }

    public function resendConfirmation(Booking $booking): JsonResponse
    {
        $booking->load('bookable');

        Mail::to($booking->guest_email)
            ->send(new BookingConfirmation($booking));

        return response()->json(['message' => 'Confirmation email sent.']);
    }

    public function sendPaymentEmail(Request $request, Booking $booking): BookingResource
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
            'payment_link' => ['required', 'url'],
        ]);

        $booking->load('bookable');

        Mail::to($booking->guest_email)
            ->send(new PaymentLinkEmail($booking, $data['message'], $data['payment_link']));

        $booking->update(['payment_email_sent_at' => now()]);

        return new BookingResource($booking->fresh('bookable'));
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }
}
