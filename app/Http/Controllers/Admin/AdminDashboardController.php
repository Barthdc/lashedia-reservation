<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalBookings = Booking::count();

        $pendingBookings = Booking::where('status', 'Pending')->count();

        $approvedBookings = Booking::where('status', 'Approved')->count();

        $rejectedBookings = Booking::where('status', 'Rejected')->count();

        $latestBookings = Booking::latest()->take(8)->get();

        /*
        |--------------------------------------------------------------------------
        | DATA DIAGRAM BATANG BERDASARKAN LAYANAN
        |--------------------------------------------------------------------------
        */

        $serviceStats = Booking::selectRaw('service, COUNT(*) as total')
            ->whereNotNull('service')
            ->groupBy('service')
            ->orderBy('total', 'desc')
            ->get();

        $maxServiceTotal = $serviceStats->max('total') ?? 1;

        /*
        |--------------------------------------------------------------------------
        | DATA DIAGRAM BATANG BERDASARKAN STATUS
        |--------------------------------------------------------------------------
        */

        $statusStats = collect([
            [
                'label' => 'Pending',
                'total' => $pendingBookings,
            ],
            [
                'label' => 'Approved',
                'total' => $approvedBookings,
            ],
            [
                'label' => 'Rejected',
                'total' => $rejectedBookings,
            ],
        ]);

        $maxStatusTotal = $statusStats->max('total') ?? 1;

        /*
        |--------------------------------------------------------------------------
        | DATA DIAGRAM BATANG BERDASARKAN STYLIST / PENATA RIAS
        |--------------------------------------------------------------------------
        */

        $stylistStats = Booking::selectRaw('stylist, COUNT(*) as total')
            ->whereNotNull('stylist')
            ->where('stylist', '!=', '')
            ->groupBy('stylist')
            ->orderBy('total', 'desc')
            ->get();

        $maxStylistTotal = $stylistStats->max('total') ?? 1;

        $powerBiEmbedUrl = env('POWER_BI_EMBED_URL');

        return view('admin.dashboard', compact(
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
            'powerBiEmbedUrl'
        ));
    }
}
