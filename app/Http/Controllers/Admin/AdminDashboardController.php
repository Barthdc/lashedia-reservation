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

        [$labelPeriod, $startDate, $endDate] = $this->getPeriodRange($period);

        $query = Booking::query();

        if ($period !== 'all') {
            $query->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ]);
        }

        $bookings = $query->latest()->get();

        $bookings = $bookings->map(function ($booking) {
            $booking->dashboard_total = $this->getBookingRevenue($booking);
            return $booking;
        });

        $totalBookings = $bookings->count();
        $pendingBookings = $bookings->where('status', 'Pending')->count();
        $approvedBookings = $bookings->where('status', 'Approved')->count();
        $rejectedBookings = $bookings->where('status', 'Rejected')->count();

        $totalRevenue = $bookings
            ->where('status', 'Approved')
            ->sum('dashboard_total');

        $latestBookings = $bookings->take(8)->values();

        $serviceStats = $bookings
            ->groupBy('service')
            ->map(function ($items, $service) {
                return (object) [
                    'service' => $service ?: 'Tidak Diketahui',
                    'total' => $items->count(),
                    'revenue' => $items->where('status', 'Approved')->sum('dashboard_total'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $maxServiceTotal = max($serviceStats->max('total') ?? 1, 1);

        $statusStats = collect([
            (object) [
                'label' => 'Pending',
                'total' => $pendingBookings,
            ],
            (object) [
                'label' => 'Approved',
                'total' => $approvedBookings,
            ],
            (object) [
                'label' => 'Rejected',
                'total' => $rejectedBookings,
            ],
        ]);

        $maxStatusTotal = max($statusStats->max('total') ?? 1, 1);

        $stylistStats = $bookings
            ->whereNotNull('stylist')
            ->where('stylist', '!=', '')
            ->groupBy('stylist')
            ->map(function ($items, $stylist) {
                return (object) [
                    'stylist' => $stylist ?: 'Tidak Diketahui',
                    'total' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $maxStylistTotal = max($stylistStats->max('total') ?? 1, 1);

        $bookingHealth = $totalBookings > 0
            ? round(($approvedBookings / $totalBookings) * 100, 1)
            : 0;

        $salesTrend = $this->getSalesTrend($bookings, $period);

        $categoryLabels = $serviceStats->pluck('service')->values();
        $categoryValues = $serviceStats->pluck('total')->values();

        $quadrantChartData = $serviceStats->map(function ($item) {
            $orders = (int) $item->total;
            $revenue = (int) $item->revenue;

            return [
                'x' => $orders,
                'y' => $revenue,
                'r' => max(8, min(28, $orders * 3)),
                'service' => $item->service,
            ];
        })->values();

        $powerBiEmbedUrl = env('POWER_BI_EMBED_URL');

        return view('admin.dashboard', compact(
            'period',
            'labelPeriod',
            'totalBookings',
            'pendingBookings',
            'approvedBookings',
            'rejectedBookings',
            'latestBookings',
            'serviceStats',
            'maxServiceTotal',
            'statusStats',
            'maxStatusTotal',
            'stylistStats',
            'maxStylistTotal',
            'totalRevenue',
            'bookingHealth',
            'salesTrend',
            'categoryLabels',
            'categoryValues',
            'quadrantChartData',
            'powerBiEmbedUrl'
        ));
    }

    private function getPeriodRange($period)
    {
        $now = Carbon::now();

        return match ($period) {
            'week' => [
                'Minggu Ini',
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
            ],
            'month' => [
                'Bulan Ini',
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
            'year' => [
                'Tahun Ini',
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
            ],
            'all' => [
                'Semua Data',
                null,
                null,
            ],
            default => [
                'Hari Ini',
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ],
        };
    }

    private function getBookingRevenue($booking)
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

    private function getSalesTrend($bookings, $period)
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
}
