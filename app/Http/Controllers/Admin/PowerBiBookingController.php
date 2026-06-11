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

        /*
        |--------------------------------------------------------------------------
        | DATA BOOKING PERIODE SEKARANG
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | DATA BOOKING PERIODE SEBELUMNYA
        |--------------------------------------------------------------------------
        */

        $previousQuery = Booking::query();

        if ($period !== 'all') {
            $previousQuery->whereBetween('date', [
                $previousStartDate->format('Y-m-d'),
                $previousEndDate->format('Y-m-d'),
            ]);
        }

        $previousBookings = $previousQuery
            ->get()
            ->map(function ($booking) {
                $booking->dashboard_total = $this->bookingRevenue($booking);
                return $booking;
            });

        /*
        |--------------------------------------------------------------------------
        | KPI UTAMA
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | DATA TOP SERVICE
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | DATA CHART
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | ALIAS VARIABLE UNTUK KOMPATIBILITAS
        |--------------------------------------------------------------------------
        | Bagian ini dibuat agar dashboard lama dan dashboard baru sama-sama aman.
        */

        $totalBookings = $totalOrders;
        $approvedBookings = $approvedCount;
        $pendingBookings = $pendingCount;
        $rejectedBookings = $rejectedCount;
        $latestBookings = $recentBookings;
        $serviceStats = $topServices;

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
            'quadrantChartData',

            'totalBookings',
            'approvedBookings',
            'pendingBookings',
            'rejectedBookings',
            'latestBookings',
            'serviceStats'
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

        return $servicePrice
            + (int) ($booking->transport_cost ?? 0)
            + (int) ($booking->biaya_transport ?? 0);
    }

    private function percentageChange($current, $previous)
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function salesTrend($bookings, $period)
    {
        if ($period === 'today') {
            $labels = collect(range(8, 22))->map(function ($hour) {
                return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            });

            $values = $labels->map(function ($label) use ($bookings) {
                return $bookings
                    ->filter(function ($booking) use ($label) {
                        if (empty($booking->time)) {
                            return false;
                        }

                        return Carbon::parse($booking->time)->format('H:00') === $label;
                    })
                    ->where('status', 'Approved')
                    ->sum('dashboard_total');
            });

            return [
                'labels' => $labels->values(),
                'values' => $values->values(),
            ];
        }

        $grouped = $bookings
            ->where('status', 'Approved')
            ->groupBy(function ($booking) {
                if (empty($booking->date)) {
                    return 'Tanpa Tanggal';
                }

                return Carbon::parse($booking->date)->format('d M');
            });

        return [
            'labels' => $grouped->keys()->values(),
            'values' => $grouped->map(function ($items) {
                return $items->sum('dashboard_total');
            })->values(),
        ];
    }

    private function heatmap($bookings)
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        $hours = collect(range(8, 22))->map(function ($hour) {
            return str_pad($hour, 2, '0', STR_PAD_LEFT);
        });

        $data = [];

        foreach ($days as $day) {
            foreach ($hours as $hour) {
                $count = $bookings->filter(function ($booking) use ($day, $hour) {
                    if (empty($booking->date) || empty($booking->time)) {
                        return false;
                    }

                    return Carbon::parse($booking->date)->format('D') === $day
                        && Carbon::parse($booking->time)->format('H') === $hour;
                })->count();

                $data[] = [
                    'day' => $day,
                    'hour' => $hour,
                    'count' => $count,
                ];
            }
        }

        return [
            'days' => $days,
            'hours' => $hours,
            'data' => collect($data),
        ];
    }

    private function quadrantData($bookings)
    {
        return $bookings
            ->groupBy('service')
            ->map(function ($items, $service) {
                return [
                    'service' => $service ?: 'Tidak Diketahui',
                    'orders' => $items->count(),
                    'revenue' => $items->where('status', 'Approved')->sum('dashboard_total'),
                ];
            })
            ->values();
    }
}
