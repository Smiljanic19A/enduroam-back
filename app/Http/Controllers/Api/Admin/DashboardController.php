<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'stats' => $this->dashboardService->getStats(),
            'recentBookings' => BookingResource::collection(
                $this->dashboardService->getRecentBookings()
            ),
            'bookingsByMonth' => $this->dashboardService->getBookingsByMonth(),
        ]);
    }
}
