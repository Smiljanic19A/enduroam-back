<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\NewsletterSubscriber;
use App\Models\Tour;
use Illuminate\Support\Carbon;

final class DashboardService
{
    public function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        return [
            'tours' => [
                'total' => Tour::count(),
                'active' => Tour::active()->count(),
            ],
            'events' => [
                'total' => Event::count(),
                'active' => Event::active()->count(),
                'upcoming' => Event::where('date', '>=', $now)->count(),
            ],
            'bookings' => [
                'total' => Booking::count(),
                'pending' => Booking::pending()->count(),
                'confirmed' => Booking::confirmed()->count(),
                'completed' => Booking::completed()->count(),
                'thisMonth' => Booking::where('created_at', '>=', $startOfMonth)->count(),
                'lastMonth' => Booking::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count(),
            ],
            'revenue' => [
                'total' => (float) Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
                'thisMonth' => (float) Booking::whereIn('status', ['confirmed', 'completed'])
                    ->where('created_at', '>=', $startOfMonth)
                    ->sum('total_price'),
                'lastMonth' => (float) Booking::whereIn('status', ['confirmed', 'completed'])
                    ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                    ->sum('total_price'),
            ],
            'newsletter' => [
                'total' => NewsletterSubscriber::count(),
                'thisMonth' => NewsletterSubscriber::where('created_at', '>=', $startOfMonth)->count(),
            ],
            'messages' => [
                'total' => ContactMessage::count(),
                'unread' => ContactMessage::unread()->count(),
            ],
        ];
    }

    public function getRecentBookings(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::with('bookable')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getBookingsByMonth(int $months = 12): array
    {
        $results = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $count = Booking::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $results[] = [
                'month' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'count' => $count,
            ];
        }

        return $results;
    }
}
