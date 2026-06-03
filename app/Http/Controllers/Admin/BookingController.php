<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::latest()->get();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function approve(Booking $booking)
    {
        $booking->update([
            'status' => 'approved'
        ]);

        return back();
    }

    public function reject(Booking $booking)
    {
        $booking->update([
            'status' => 'rejected'
        ]);

        return back();
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return back();
    }

}
