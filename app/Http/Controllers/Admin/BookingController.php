<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingInvoiceMail;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::latest()->get();

        return view('admin.bookings.index', compact('bookings'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE BOOKING + GENERATE INVOICE + SEND EMAIL
    |--------------------------------------------------------------------------
    */

    public function approve(Booking $booking)
    {
        $invoiceNumber = $booking->invoice_number;

        if (!$invoiceNumber) {
            $invoiceNumber = $this->generateInvoiceNumber($booking);
        }

        $servicePrice = $this->getServicePrice($booking->service);
        $transportCost = (int) ($booking->transport_cost ?? 0);
        $invoiceTotal = $servicePrice + $transportCost;

        $booking->update([
            'status' => 'Approved',
            'reject_reason' => null,

            'invoice_number' => $invoiceNumber,
            'invoice_date' => now(),
            'invoice_subtotal' => $servicePrice,
            'invoice_transport' => $transportCost,
            'invoice_total' => $invoiceTotal,
        ]);

        try {
            Mail::to($booking->email)->send(
                new BookingInvoiceMail($booking->fresh())
            );

            $booking->update([
                'invoice_sent_at' => now(),
            ]);

            return back()->with(
                'success',
                'Booking berhasil disetujui dan invoice otomatis dikirim ke email pelanggan.'
            );

        } catch (\Exception $e) {

            Log::error('Gagal mengirim invoice email: ' . $e->getMessage());

            return back()->with(
                'success',
                'Booking berhasil disetujui, tetapi invoice gagal dikirim ke email pelanggan. Cek konfigurasi MAIL di file .env.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PENDING BOOKING
    |--------------------------------------------------------------------------
    */

    public function pending(Booking $booking)
    {
        $booking->update([
            'status' => 'Pending',
            'reject_reason' => null,
        ]);

        return back()->with(
            'success',
            'Status booking berhasil dikembalikan ke Pending.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT BOOKING
    |--------------------------------------------------------------------------
    */

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $booking->update([
            'status' => 'Rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with(
            'success',
            'Booking berhasil ditolak.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE BOOKING
    |--------------------------------------------------------------------------
    */

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return back()->with(
            'success',
            'Booking berhasil dihapus.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE NOMOR INVOICE
    |--------------------------------------------------------------------------
    */

    private function generateInvoiceNumber(Booking $booking)
    {
        return 'INV-LASHEDIA-' .
            Carbon::now()->format('Ymd') .
            '-' .
            str_pad($booking->id, 5, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | HARGA LAYANAN
    |--------------------------------------------------------------------------
    | Silakan ubah nominal di bawah ini sesuai harga asli Lashedia.
    |--------------------------------------------------------------------------
    */

    private function getServicePrice($service)
    {
        return match ($service) {
            'Wedding Makeup' => 1500000,
            'Wisuda Makeup' => 350000,
            'Eyelash Extension' => 250000,
            'Nail Art' => 150000,
            'Hair Do' => 200000,
            default => 0,
        };
    }
}
