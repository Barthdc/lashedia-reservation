<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::latest()->get();

        return view(
            'admin.bookings.index',
            compact('bookings')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(Booking $booking)
    {
        $booking->update([

            'status' => 'Approved',

            'reject_reason' => null

        ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | PENDING
    |--------------------------------------------------------------------------
    */

    public function pending(Booking $booking)
    {
        $booking->update([

            'status' => 'Pending',

            'reject_reason' => null

        ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        Booking $booking
    )
    {
        $request->validate([

            'reject_reason' => 'required'

        ]);

        $booking->update([

            'status' => 'Rejected',

            'reject_reason'
                => $request->reject_reason

        ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return back();
    }
}
