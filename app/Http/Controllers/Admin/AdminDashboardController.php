<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        [$labelPeriod, $startDate, $endDate, $previousStartDate, $previousEndDate] =
            $this->periodRange($period);

        $bookingQuery = Booking::query();

        if ($period !== 'all') {
            $bookingQuery->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ]);
        }

        $bookings = $bookingQuery
            ->latest()
            ->get()
            ->map(function ($booking) {
                $booking->dashboard_total = $this->bookingRevenue($booking);
                return $booking;
            });

        $previousBookingQuery = Booking::query();

        if ($period !== 'all') {
            $previousBookingQuery->whereBetween('date', [
                $previousStartDate->format('Y-m-d'),
                $previousEndDate->format('Y-m-d'),
            ]);
        }

        $previousBookings = $previousBookingQuery
            ->get()
            ->map(function ($booking) {
                $booking->dashboard_total = $this->bookingRevenue($booking);
                return $booking;
            });

        $totalOrders = $bookings->count();
        $unitsSold = $bookings->count();

        $approvedCount = $bookings->where('status', 'Approved')->count();
        $pendingCount = $bookings->where('status', 'Pending')->count();
        $rejectedCount = $bookings->where('status', 'Rejected')->count();

        $totalRevenue = $bookings
            ->where('status', 'Approved')
            ->sum('dashboard_total');

        $previousOrders = $previousBookings->count();

        $previousRevenue = $previousBookings
            ->where('status', 'Approved')
            ->sum('dashboard_total');

        $ordersChange = $this->percentageChange($totalOrders, $previousOrders);
        $revenueChange = $this->percentageChange($totalRevenue, $previousRevenue);

        $bookingHealth = $totalOrders > 0
            ? round(($approvedCount / $totalOrders) * 100, 1)
            : 0;

        $topServices = $bookings
            ->groupBy('service')
            ->map(function ($items, $service) {
                return [
                    'service' => $service ?: 'Tidak Diketahui',
                    'total' => $items->count(),
                    'revenue' => $items->where('status', 'Approved')->sum('dashboard_total'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $categoryContribution = $topServices->map(function ($item) use ($unitsSold) {
            $item['percentage'] = $unitsSold > 0
                ? round(($item['total'] / $unitsSold) * 100, 1)
                : 0;

            return $item;
        });

        $salesTrend = $this->salesTrend($bookings, $period);
        $heatmap = $this->heatmap($bookings);
        $quadrantData = $this->quadrantData($bookings);

        $recentBookings = $bookings->take(6)->values();

        $bestService = $topServices->first()['service'] ?? '-';
        $topRevenue = $topServices->max('revenue') ?? 0;

        $categoryLabels = $categoryContribution->pluck('service')->values();
        $categoryValues = $categoryContribution->pluck('total')->values();

        $quadrantChartData = $quadrantData->map(function ($item) {
            $orders = (int) $item['orders'];
            $revenue = (int) $item['revenue'];

            return [
                'x' => $orders,
                'y' => $revenue,
                'r' => max(8, min(28, $orders * 3)),
                'service' => $item['service'],
            ];
        })->values();

        return view('admin.dashboard', compact(
            'period',
            'labelPeriod',
            'totalOrders',
            'unitsSold',
            'totalRevenue',
            'approvedCount',
            'pendingCount',
            'rejectedCount',
            'ordersChange',
            'revenueChange',
            'bookingHealth',
            'topServices',
            'categoryContribution',
            'salesTrend',
            'heatmap',
            'recentBookings',
            'quadrantData',
            'bestService',
            'topRevenue',
            'categoryLabels',
            'categoryValues',
            'quadrantChartData'
        ));
    }

    private function periodRange($period)
    {
        $now = Carbon::now();

        return match ($period) {
            'week' => [
                'Minggu Ini',
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
                $now->copy()->subWeek()->startOfWeek(),
                $now->copy()->subWeek()->endOfWeek(),
            ],

            'month' => [
                'Bulan Ini',
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ],

            'year' => [
                'Tahun Ini',
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
            ],

            'all' => [
                'Semua Data',
                null,
                null,
                null,
                null,
            ],

            default => [
                'Hari Ini',
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
            ],
        };
    }

    private function bookingRevenue($booking)
    {
        if (!empty($booking->invoice_total)) {
            return (int) $booking->invoice_total;
        }

        if (!empty($booking->total_price)) {
            return (int) $booking->total_price;
        }

        if (!empty($booking->price)) {
            return (int) $booking->price;
        }

        $servicePrice = match ($booking->service) {
            'Wedding Makeup', 'Make Up Wedding', 'Makeup Wedding', 'Wedding' => 1500000,
            'Graduation Makeup', 'Make Up Wisuda', 'Wisuda Makeup', 'Wisuda' => 350000,
            'Party Makeup', 'Make Up Party', 'Party' => 300000,
            'Hair Do' => 200000,
            'Nail Art' => 150000,
            'Eyelash Extension' => 250000,
            default => 0,
        };

        return $servicePrice;
    }
}
