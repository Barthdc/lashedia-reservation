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
        /*
        |--------------------------------------------------------------------------
        | Ambil filter dari URL
        |--------------------------------------------------------------------------
        | Contoh:
        | /admin/dashboard?period=today
        | /admin/dashboard?period=week
        | /admin/dashboard?period=month
        */

        $period = $request->get('period', 'today');

        /*
        |--------------------------------------------------------------------------
        | Ambil range tanggal berdasarkan filter
        |--------------------------------------------------------------------------
        */

        [$labelPeriod, $startDate, $endDate, $previousStartDate, $previousEndDate] =
            $this->getPeriodRange($period);

        /*
        |--------------------------------------------------------------------------
        | Query data booking periode saat ini
        |--------------------------------------------------------------------------
        */

        $bookingQuery = Booking::query();

        if ($period !== 'all') {
            $bookingQuery->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ]);
        }

        $bookings = $bookingQuery->latest()->get();

        /*
        |--------------------------------------------------------------------------
        | Query data booking periode sebelumnya
        |--------------------------------------------------------------------------
        | Digunakan untuk menghitung naik/turun dari periode sebelumnya.
        */

        $previousBookingQuery = Booking::query();

        if ($period !== 'all') {
            $previousBookingQuery->whereBetween('date', [
                $previousStartDate->format('Y-m-d'),
                $previousEndDate->format('Y-m-d')
            ]);
        }

        $previousBookings = $previousBookingQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Tambahkan total harga ke setiap booking
        |--------------------------------------------------------------------------
        | Karena di project kamu belum tentu ada field total_price,
        | maka total dihitung berdasarkan jenis layanan.
        */

        $bookings = $bookings->map(function ($booking) {
            $booking->dashboard_total = $this->getServicePrice($booking->service);
            return $booking;
        });

        $previousBookings = $previousBookings->map(function ($booking) {
            $booking->dashboard_total = $this->getServicePrice($booking->service);
            return $booking;
        });

        /*
        |--------------------------------------------------------------------------
        | KPI utama
        |--------------------------------------------------------------------------
        */

        $totalOrders = $bookings->count();

        $unitsSold = $bookings->count();

        $totalRevenue = $bookings
            ->where('status', 'Approved')
            ->sum('dashboard_total');

        $approvedCount = $bookings->where('status', 'Approved')->count();
        $pendingCount = $bookings->where('status', 'Pending')->count();
        $rejectedCount = $bookings->where('status', 'Rejected')->count();

        $bookingHealth = $totalOrders > 0
            ? round(($approvedCount / $totalOrders) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Data periode sebelumnya untuk persentase naik/turun
        |--------------------------------------------------------------------------
        */

        $previousOrders = $previousBookings->count();

        $previousRevenue = $previousBookings
            ->where('status', 'Approved')
            ->sum('dashboard_total');

        $ordersChange = $this->getPercentageChange($totalOrders, $previousOrders);
        $revenueChange = $this->getPercentageChange($totalRevenue, $previousRevenue);

        /*
        |--------------------------------------------------------------------------
        | Top Service Performance
        |--------------------------------------------------------------------------
        */

        $topServices = $bookings
            ->groupBy('service')
            ->map(function ($items, $service) {
                return [
                    'service' => $service ?? 'Tidak Diketahui',
                    'total' => $items->count(),
                    'revenue' => $items->where('status', 'Approved')->sum('dashboard_total'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Category Contribution
        |--------------------------------------------------------------------------
        */

        $categoryContribution = $topServices->map(function ($item) use ($unitsSold) {
            $item['percentage'] = $unitsSold > 0
                ? round(($item['total'] / $unitsSold) * 100, 1)
                : 0;

            return $item;
        });

        /*
        |--------------------------------------------------------------------------
        | Sales Trend
        |--------------------------------------------------------------------------
        */

        $salesTrend = $this->getSalesTrend($bookings, $period);

        /*
        |--------------------------------------------------------------------------
        | Sales Heatmap
        |--------------------------------------------------------------------------
        */

        $heatmap = $this->getHeatmapData($bookings);

        /*
        |--------------------------------------------------------------------------
        | Recent Timeline
        |--------------------------------------------------------------------------
        */

        $recentBookings = $bookings->take(5)->values();

        /*
        |--------------------------------------------------------------------------
        | Quadrant Analysis
        |--------------------------------------------------------------------------
        */

        $quadrantData = $this->getQuadrantData($bookings);

        $bestService = $topServices->first()['service'] ?? '-';
        $topRevenue = $topServices->max('revenue') ?? 0;

        return view('admin.dashboard', compact(
            'period',
            'labelPeriod',
            'totalOrders',
            'unitsSold',
            'totalRevenue',
            'approvedCount',
            'pendingCount',
            'rejectedCount',
            'bookingHealth',
            'ordersChange',
            'revenueChange',
            'topServices',
            'categoryContribution',
            'salesTrend',
            'heatmap',
            'recentBookings',
            'quadrantData',
            'bestService',
            'topRevenue'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Fungsi range periode
    |--------------------------------------------------------------------------
    */

    private function getPeriodRange($period)
    {
        $now = Carbon::now();

        if ($period === 'week') {
            return [
                'Minggu Ini',
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
                $now->copy()->subWeek()->startOfWeek(),
                $now->copy()->subWeek()->endOfWeek(),
            ];
        }

        if ($period === 'month') {
            return [
                'Bulan Ini',
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ];
        }

        if ($period === 'year') {
            return [
                'Tahun Ini',
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
            ];
        }

        if ($period === 'all') {
            return [
                'Semua Data',
                null,
                null,
                null,
                null,
            ];
        }

        return [
            'Hari Ini',
            $now->copy()->startOfDay(),
            $now->copy()->endOfDay(),
            $now->copy()->subDay()->startOfDay(),
            $now->copy()->subDay()->endOfDay(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Fungsi harga layanan
    |--------------------------------------------------------------------------
    | Silakan sesuaikan harga dengan paket Lashedia kamu.
    */

    private function getServicePrice($service)
    {
        return match ($service) {
            'Wedding Makeup' => 1500000,
            'Make Up Wedding' => 1500000,
            'Makeup Wedding' => 1500000,

            'Graduation Makeup' => 350000,
            'Make Up Wisuda' => 350000,
            'Wisuda Makeup' => 350000,

            'Party Makeup' => 300000,
            'Make Up Party' => 300000,

            'Hair Do' => 200000,
            'Nail Art' => 150000,
            'Eyelash Extension' => 250000,

            default => 0,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Fungsi hitung persentase naik/turun
    |--------------------------------------------------------------------------
    */

    private function getPercentageChange($current, $previous)
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Fungsi sales trend
    |--------------------------------------------------------------------------
    */

    private function getSalesTrend($bookings, $period)
    {
        if ($period === 'today') {
            $hours = collect(range(8, 22))->map(function ($hour) {
                return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            });

            $values = $hours->map(function ($hour) use ($bookings) {
                return $bookings
                    ->filter(function ($booking) use ($hour) {
                        return Carbon::parse($booking->time)->format('H:00') === $hour;
                    })
                    ->where('status', 'Approved')
                    ->sum('dashboard_total');
            });

            return [
                'labels' => $hours,
                'values' => $values,
            ];
        }

        $grouped = $bookings
            ->where('status', 'Approved')
            ->groupBy(function ($booking) {
                return Carbon::parse($booking->date)->format('d M');
            });

        return [
            'labels' => $grouped->keys()->values(),
            'values' => $grouped->map(function ($items) {
                return $items->sum('dashboard_total');
            })->values(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Fungsi heatmap
    |--------------------------------------------------------------------------
    */

    private function getHeatmapData($bookings)
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        $hours = collect(range(8, 22))->map(function ($hour) {
            return str_pad($hour, 2, '0', STR_PAD_LEFT);
        });

        $data = [];

        foreach ($days as $day) {
            foreach ($hours as $hour) {
                $count = $bookings->filter(function ($booking) use ($day, $hour) {
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

    /*
    |--------------------------------------------------------------------------
    | Fungsi quadrant analysis
    |--------------------------------------------------------------------------
    */

    private function getQuadrantData($bookings)
    {
        return $bookings
            ->groupBy('service')
            ->map(function ($items, $service) {
                return [
                    'service' => $service ?? 'Tidak Diketahui',
                    'orders' => $items->count(),
                    'revenue' => $items->where('status', 'Approved')->sum('dashboard_total'),
                ];
            })
            ->values();
    }
}
