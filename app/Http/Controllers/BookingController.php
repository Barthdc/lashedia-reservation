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

        /*
        |--------------------------------------------------------------------------
        | BLOKIR TANGGAL WEDDING BERDASARKAN STYLIST
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | BLOKIR JAM 3 JAM KE DEPAN BERDASARKAN STYLIST
        |--------------------------------------------------------------------------
        */

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
            'village' => 'required',
            'district' => 'required',
            'city' => 'required',
            'province' => 'required',
            'island' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'flight_ticket_cost' => 'nullable|integer|min:0',

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

        /*
        |--------------------------------------------------------------------------
        | VALIDASI 1:
        | JIKA ADA WEDDING PADA STYLIST YANG SAMA,
        | TANGGAL TERSEBUT DIBLOKIR UNTUK STYLIST ITU SAJA
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | VALIDASI 2:
        | JIKA USER MEMILIH WEDDING,
        | CEK APAKAH STYLIST YANG SAMA SUDAH ADA BOOKING DI TANGGAL ITU
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | VALIDASI 3:
        | BLOKIR JAM 3 JAM KE DEPAN HANYA UNTUK STYLIST YANG SAMA
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | HITUNG ONGKIR LOKASI
        |--------------------------------------------------------------------------
        */

        $locationCost = $this->calculateLocationCost(
            $request->province,
            $request->city,
            $request->island,
            $request->flight_ticket_cost ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | UPLOAD BUKTI PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $paymentProofPath = null;

        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request
                ->file('payment_proof')
                ->store('payment_proofs', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN BOOKING
        |--------------------------------------------------------------------------
        */

        $booking = Booking::create([
            'user_id' => Auth::id(),

            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,

            'full_address' => $request->full_address,
            'village' => $request->village,
            'district' => $request->district,
            'city' => $request->city,
            'province' => $request->province,
            'island' => $request->island,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,

            'shipping_zone' => $locationCost['zone'],
            'shipping_cost' => $locationCost['shipping_cost'],
            'flight_ticket_cost' => $locationCost['flight_ticket_cost'],
            'total_location_cost' => $locationCost['total_location_cost'],

            'service' => $request->service,
            'date' => $bookingDate,
            'time' => $request->time,
            'note' => $request->note,
            'stylist' => $selectedStylist,
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
            'status' => 'Pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | KIRIM KE GOOGLE CALENDAR
        |--------------------------------------------------------------------------
        */

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

    private function calculateLocationCost($province, $city, $island, $flightTicketCost = 0)
    {
        $province = strtolower(trim($province ?? ''));
        $city = strtolower(trim($city ?? ''));
        $island = strtolower(trim($island ?? ''));

        $jabodetabekCities = [
            'jakarta',
            'jakarta pusat',
            'jakarta barat',
            'jakarta timur',
            'jakarta utara',
            'jakarta selatan',
            'bogor',
            'kota bogor',
            'kabupaten bogor',
            'depok',
            'kota depok',
            'tangerang',
            'kota tangerang',
            'kabupaten tangerang',
            'tangerang selatan',
            'kota tangerang selatan',
            'bekasi',
            'kota bekasi',
            'kabupaten bekasi',
        ];

        /*
        |--------------------------------------------------------------------------
        | PRIORITAS 1: LUAR PULAU
        |--------------------------------------------------------------------------
        */

        if ($island !== '' && $island !== 'jawa') {
            return [
                'zone' => 'Luar Pulau',
                'shipping_cost' => 800000,
                'flight_ticket_cost' => (int) $flightTicketCost,
                'total_location_cost' => 800000 + (int) $flightTicketCost,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PRIORITAS 2: LUAR PROVINSI BANTEN
        |--------------------------------------------------------------------------
        */

        if ($province !== '' && $province !== 'banten') {
            return [
                'zone' => 'Luar Provinsi Banten',
                'shipping_cost' => 400000,
                'flight_ticket_cost' => 0,
                'total_location_cost' => 400000,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PRIORITAS 3: JABODETABEK
        |--------------------------------------------------------------------------
        */

        if (in_array($city, $jabodetabekCities)) {
            return [
                'zone' => 'Jabodetabek',
                'shipping_cost' => 100000,
                'flight_ticket_cost' => 0,
                'total_location_cost' => 100000,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT: LUAR JABODETABEK
        |--------------------------------------------------------------------------
        */

        return [
            'zone' => 'Luar Jabodetabek',
            'shipping_cost' => 200000,
            'flight_ticket_cost' => 0,
            'total_location_cost' => 200000,
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
