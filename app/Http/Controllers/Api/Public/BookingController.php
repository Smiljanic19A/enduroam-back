<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;

final class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function store(StoreBookingRequest $request): BookingResource
    {
        $booking = $this->bookingService->createBooking($request->validated());

        return new BookingResource($booking);
    }
}
