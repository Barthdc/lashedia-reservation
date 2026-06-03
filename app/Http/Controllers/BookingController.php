<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleCalendarService;

class BookingController extends Controller
{
    public function create()
    {
        $googleCalendarConnected = file_exists(
            storage_path('app/google-calendar-token.json')
        );

        return view(
            'pesan-sekarang',
            compact('googleCalendarConnected')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'service' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'note' => 'nullable',
            'stylist' => 'nullable',
            'payment_method' => 'nullable',
            'payment_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $paymentProofPath = null;

        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request
                ->file('payment_proof')
                ->store('payment_proofs', 'public');
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'service' => $request->service,
            'date' => $request->date,
            'time' => $request->time,
            'note' => $request->note,
            'stylist' => $request->stylist,
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
            'status' => 'Pending',
        ]);

        try {
            app(GoogleCalendarService::class)->createBookingEvent($booking);
        } catch (\Exception $e) {
            Log::error('Google Calendar Error: ' . $e->getMessage());

            return back()->with(
                'success',
                'Booking berhasil dikirim. Namun sinkronisasi ke Google Calendar belum berhasil.'
            );
        }

        return back()->with(
            'success',
            'Booking berhasil dikirim dan masuk ke Google Calendar.'
        );
    }

    public function riwayat()
    {
        if (Auth::user()->role === 'admin') {
            $bookings = Booking::latest()->get();
        } else {
            $bookings = Booking::where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return view(
            'riwayat',
            compact('bookings')
        );
    }

    public function notifications()
    {
        if (Auth::user()->role === 'admin') {
            $bookings = Booking::latest()->get();
        } else {
            $bookings = Booking::where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return view(
            'notifikasi',
            compact('bookings')
        );
    }
}
