<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }
}
