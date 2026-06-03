@extends('layouts.app')

@section('content')

@php

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

if (Auth::user()->role == 'admin') {

    // ADMIN MELIHAT SEMUA BOOKING
    $bookings = Booking::latest()->get();

} else {

    // USER HANYA MELIHAT BOOKING MILIKNYA
    $bookings = Booking::where(
        'user_id',
        Auth::id()
    )->latest()->get();

}

@endphp

<div class="notif-wrapper">

    <h1 class="notif-title">
        Notifikasi Booking
    </h1>

    <p class="notif-subtitle">
        Lihat status booking dan informasi terbaru dari admin.
    </p>

    @forelse($bookings as $booking)

        <div class="notif-card">

            <div class="notif-top">

                <div class="notif-name">
                    {{ $booking->service }}
                </div>

                <div class="notif-status">

                    {{ ucfirst($booking->status) }}

                </div>

            </div>

            <p>
                <strong>Tanggal:</strong>
                {{ $booking->date }}
            </p>

            <p>
                <strong>Jam:</strong>
                {{ $booking->time }}
            </p>

            <p>
                <strong>Stylist:</strong>
                {{ $booking->stylist }}
            </p>

            <p>
                <strong>Pembayaran:</strong>
                {{ $booking->payment_method }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ $booking->status }}
            </p>

        </div>

    @empty

        <div class="notif-card">

            Belum ada notifikasi booking.

        </div>

    @endforelse

</div>

@endsection
