@extends('layouts.app')

@section('content')

<div class="riwayat-container">

    <div class="riwayat-header">

        <h1>Riwayat Booking</h1>



    </div>

    @forelse($bookings as $booking)

        <div class="riwayat-card">

            <div class="riwayat-top">

                <div>

                    <h3>
                        {{ $booking->service }}
                    </h3>

                    <span>
                        {{ $booking->date }}
                        -
                        {{ $booking->time }}
                    </span>

                </div>

                <div>

                    @if($booking->status == 'Approved')

                        <span class="status approved">
                            Approved
                        </span>

                    @elseif($booking->status == 'Rejected')

                        <span class="status rejected">
                            Rejected
                        </span>

                    @else

                        <span class="status pending">
                            Pending
                        </span>

                    @endif

                </div>

            </div>

            <div class="riwayat-content">

                <p>
                    <strong>Stylist:</strong>
                    {{ $booking->stylist }}
                </p>

                <p>
                    <strong>Pembayaran:</strong>
                    {{ $booking->payment_method }}
                </p>

                <p>
                    <strong>Catatan:</strong>
                    {{ $booking->note }}
                </p>

            </div>

            @if($booking->reject_reason)

                <div class="reject-alert">

                    <strong>
                        Alasan Penolakan:
                    </strong>

                    <p>
                        {{ $booking->reject_reason }}
                    </p>

                </div>

            @endif

        </div>

    @empty

        <div class="empty-riwayat">

            Belum ada booking.

        </div>

    @endforelse

</div>

<style>

.riwayat-container{
    max-width:1000px;
    margin:auto;
    padding:40px 20px;
}

.riwayat-header{
    margin-bottom:35px;
}

.riwayat-header h1{
    font-size:38px;
    margin-bottom:8px;
}

.riwayat-header p{
    color:#777;
}

.riwayat-card{
    background:white;
    border-radius:24px;
    padding:28px;
    margin-bottom:25px;
    border:1px solid #f0d9e2;
}

.riwayat-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.riwayat-top h3{
    font-size:24px;
    margin-bottom:5px;
}

.status{
    padding:10px 18px;
    border-radius:999px;
    font-size:13px;
    font-weight:700;
}

.status.approved{
    background:#dcfce7;
    color:#166534;
}

.status.rejected{
    background:#ffe2e2;
    color:#991b1b;
}

.status.pending{
    background:#fff4d6;
    color:#b7791f;
}

.reject-alert{
    margin-top:18px;
    background:#fff1f2;
    padding:18px;
    border-radius:14px;
}

.empty-riwayat{
    background:white;
    padding:40px;
    border-radius:20px;
    text-align:center;
    color:#888;
}

</style>

@endsection
