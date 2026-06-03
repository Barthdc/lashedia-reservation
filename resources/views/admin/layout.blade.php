@extends('layouts.app')

@section('content')

<div class="admin-wrapper">

    <!-- NAVBAR -->
    <nav class="admin-navbar">

        <div class="admin-logo">
            Lashedia Admin
        </div>

        <div class="admin-right">

            <ul class="admin-menu">

                <li>
                    <a href="{{ route('admin.bookings.index') }}"
                       class="{{ request()->is('admin/bookings*') ? 'active' : '' }}">
                        Booking
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.gallery.index') }}"
                       class="{{ request()->is('admin/gallery*') ? 'active' : '' }}">
                        Gallery
                    </a>
                </li>
            </li>
            <a href="{{ route('google.auth') }}" class="btn btn-primary">
    Hubungkan Google Calendar
            </a>

                <li>

                    @auth


            </ul>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="admin-btn">
                    Logout
                </button>
            </form>

        </div>

    </nav>

    <!-- CONTENT -->
    <div class="admin-booking-container">

        <div class="booking-header">
            <h1>Daftar Booking Customer</h1>

            <p>
                Kelola semua pesanan customer Lashedia
            </p>
        </div>

        @forelse($bookings as $booking)

            <div class="booking-card">

                <div class="booking-top">

                    <div>
                        <h2>{{ $booking->name }}</h2>

                        <span class="booking-date">
                            {{ $booking->created_at->format('d M Y H:i') }}
                        </span>
                    </div>

                    <div>
                        @if($booking->status == 'pending')

                            <span class="status pending">
                                Pending
                            </span>

                        @elseif($booking->status == 'approved')

                            <span class="status approved">
                                Approved
                            </span>

                        @else

                            <span class="status rejected">
                                Rejected
                            </span>

                        @endif
                    </div>

                </div>

                <div class="booking-info">

                    <div class="info-item">
                        <strong>Email</strong>
                        <p>{{ $booking->email }}</p>
                    </div>

                    <div class="info-item">
                        <strong>Phone</strong>
                        <p>{{ $booking->phone }}</p>
                    </div>

                    <div class="info-item">
                        <strong>Tanggal Booking</strong>
                        <p>{{ $booking->booking_date }}</p>
                    </div>

                    <div class="info-item">
                        <strong>Jam</strong>
                        <p>{{ $booking->booking_time }}</p>
                    </div>

                    <div class="info-item">
                        <strong>Layanan</strong>
                        <p>{{ $booking->service }}</p>
                    </div>

                    <div class="info-item full">
                        <strong>Catatan</strong>
                        <p>{{ $booking->message }}</p>
                    </div>

                </div>

                <!-- ACTION -->
                <div class="booking-actions">

                    @if($booking->status == 'pending')

                        <form action="{{ route('admin.bookings.approve', $booking->id) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')

                            <button class="approve-btn">
                                Approve
                            </button>
                        </form>

                        <form action="{{ route('admin.bookings.reject', $booking->id) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')

                            <button class="reject-btn">
                                Reject
                            </button>
                        </form>

                    @endif

                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}"
                          method="POST">

                        @csrf
                        @method('DELETE')

                        <button class="delete-btn">
                            Delete
                        </button>

                    </form>

                </div>

            </div>

        @empty

            <div class="empty-booking">

                Belum ada booking masuk.

            </div>

        @endforelse

    </div>

</div>

@endsection
