<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class PowerBiBookingController extends Controller
{
    public function bookingsCsv()
    {
        $fileName = 'lashedia-bookings-powerbi.csv';

        $bookings = Booking::latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Cache-Control' => 'no-store, no-cache',
        ];

        $callback = function () use ($bookings) {

            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'ID Booking',
                'Nama Pelanggan',
                'Email',
                'Nomor WhatsApp',
                'Layanan',
                'Tanggal Booking',
                'Jam Booking',
                'Penata Rias',
                'Metode Pembayaran',
                'Status',
                'Catatan',
                'Tanggal Dibuat',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->name,
                    $booking->email,
                    $booking->phone,
                    $booking->service,
                    $booking->date,
                    $booking->time,
                    $booking->stylist,
                    $booking->payment_method,
                    $booking->status,
                    $booking->note,
                    optional($booking->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
