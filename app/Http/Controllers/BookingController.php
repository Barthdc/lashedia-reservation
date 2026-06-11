<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create()
    {
        $googleCalendarConnected = file_exists(
            storage_path('app/google-calendar-token.json')
        );

        $blockedWeddingDates = Booking::where('service', 'Wedding Makeup')
            ->where('status', '!=', 'Rejected')
            ->whereNotNull('stylist')
            ->where('stylist', '!=', '')
            ->get()
            ->groupBy('stylist')
            ->map(function ($bookings) {
                return $bookings->pluck('date')
                    ->map(function ($date) {
                        return Carbon::parse($date)->format('Y-m-d');
                    })
                    ->unique()
                    ->values();
            });

        $timeBlocks = Booking::where('status', '!=', 'Rejected')
            ->whereNotNull('stylist')
            ->where('stylist', '!=', '')
            ->get()
            ->groupBy('stylist')
            ->map(function ($stylistBookings) {
                return $stylistBookings
                    ->groupBy(function ($booking) {
                        return Carbon::parse($booking->date)->format('Y-m-d');
                    })
                    ->map(function ($dateBookings) {
                        return $dateBookings->map(function ($booking) {
                            $bookingDate = Carbon::parse($booking->date)->format('Y-m-d');

                            $start = Carbon::parse($bookingDate . ' ' . $booking->time);
                            $end = $start->copy()->addHours(3);

                            return [
                                'start' => $start->format('H:i'),
                                'end' => $end->format('H:i'),
                                'service' => $booking->service,
                                'stylist' => $booking->stylist,
                                'customer' => $booking->name,
                            ];
                        })->values();
                    });
            });

        return view('pesan-sekarang', compact(
            'googleCalendarConnected',
            'blockedWeddingDates',
            'timeBlocks'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',

            'full_address' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',

            'service' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'note' => 'nullable',
            'stylist' => 'required',
            'payment_method' => 'nullable',
            'payment_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $bookingDate = Carbon::parse($request->date)->format('Y-m-d');
        $selectedStylist = $request->stylist;

        $newStart = Carbon::parse($bookingDate . ' ' . $request->time);
        $newEnd = $newStart->copy()->addHours(3);

        $hasWeddingBookingForSameStylist = Booking::whereDate('date', $bookingDate)
            ->where('service', 'Wedding Makeup')
            ->where('stylist', $selectedStylist)
            ->where('status', '!=', 'Rejected')
            ->exists();

        if ($hasWeddingBookingForSameStylist) {
            return back()
                ->withInput()
                ->withErrors([
                    'date' => 'Tanggal ini sudah diblokir karena stylist ' . $selectedStylist . ' memiliki booking Wedding Makeup.',
                ]);
        }

        if ($request->service === 'Wedding Makeup') {
            $hasAnyBookingForSameStylistOnDate = Booking::whereDate('date', $bookingDate)
                ->where('stylist', $selectedStylist)
                ->where('status', '!=', 'Rejected')
                ->exists();

            if ($hasAnyBookingForSameStylistOnDate) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'date' => 'Wedding Makeup membutuhkan 1 hari penuh. Stylist ' . $selectedStylist . ' sudah memiliki booking pada tanggal ini.',
                    ]);
            }
        }

        $bookingsOnDateForSameStylist = Booking::whereDate('date', $bookingDate)
            ->where('stylist', $selectedStylist)
            ->where('status', '!=', 'Rejected')
            ->get();

        foreach ($bookingsOnDateForSameStylist as $existingBooking) {
            $existingStart = Carbon::parse($bookingDate . ' ' . $existingBooking->time);
            $existingEnd = $existingStart->copy()->addHours(3);

            $isOverlapping = $newStart->lt($existingEnd) && $newEnd->gt($existingStart);

            if ($isOverlapping) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'time' => 'Jam ini tidak tersedia untuk stylist ' .
                            $selectedStylist .
                            '. Sudah ada booking dari jam ' .
                            $existingStart->format('H:i') .
                            ' sampai ' .
                            $existingEnd->format('H:i') .
                            '.',
                    ]);
            }
        }

        $muaLatitude = env('MUA_LATITUDE', -6.170170);
        $muaLongitude = env('MUA_LONGITUDE', 106.640300);

        $distanceKm = $this->calculateDistanceKm(
            $muaLatitude,
            $muaLongitude,
            $request->latitude,
            $request->longitude
        );

        $transport = $this->calculateTransportCost($distanceKm);

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

            'full_address' => $request->full_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,

            'mua_latitude' => $muaLatitude,
            'mua_longitude' => $muaLongitude,
            'distance_km' => $distanceKm,
            'transport_cost' => $transport['cost'],
            'transport_note' => $transport['note'],

            'service' => $request->service,
            'date' => $bookingDate,
            'time' => $request->time,
            'note' => $request->note,
            'stylist' => $selectedStylist,
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

    private function calculateDistanceKm($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $lat1 = deg2rad((float) $lat1);
        $lon1 = deg2rad((float) $lon1);
        $lat2 = deg2rad((float) $lat2);
        $lon2 = deg2rad((float) $lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    private function calculateTransportCost($distanceKm)
    {
        $distanceKm = (float) $distanceKm;

        if ($distanceKm <= 5) {
            return [
                'cost' => 15000,
                'note' => 'Jarak 0–5 km',
            ];
        }

        if ($distanceKm <= 10) {
            return [
                'cost' => 25000,
                'note' => 'Jarak 6–10 km',
            ];
        }

        if ($distanceKm <= 15) {
            return [
                'cost' => 40000,
                'note' => 'Jarak 11–15 km',
            ];
        }

        if ($distanceKm <= 20) {
            return [
                'cost' => 60000,
                'note' => 'Jarak 16–20 km',
            ];
        }

        if ($distanceKm <= 30) {
            return [
                'cost' => 90000,
                'note' => 'Jarak 21–30 km',
            ];
        }

        if ($distanceKm <= 40) {
            return [
                'cost' => 120000,
                'note' => 'Jarak 31–40 km',
            ];
        }

        $ratePerKm = (int) env('LONG_DISTANCE_RATE_PER_KM', 5000);
        $extraFee = (int) env('LONG_DISTANCE_EXTRA_FEE', 50000);

        $cost = ceil($distanceKm) * $ratePerKm + $extraFee;

        return [
            'cost' => $cost,
            'note' => 'Jarak di atas 40 km. Biaya dihitung Rp' .
                number_format($ratePerKm, 0, ',', '.') .
                ' per km + biaya tambahan Rp' .
                number_format($extraFee, 0, ',', '.'),
        ];
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

        return view('riwayat', compact('bookings'));
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

        return view('notifikasi', compact('bookings'));
    }
}
