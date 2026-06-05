@extends('layouts.admin')

@section('content')

<div class="admin-booking-container">

    <div class="booking-header">

        <h1>Daftar Booking Customer</h1>

        <p>
            Kelola semua pesanan customer Lashedia
        </p>

    </div>

    @forelse($bookings as $booking)

        <div class="booking-item">

            <div class="booking-top">

                <div>

                    <h3>
                        {{ $booking->name }}
                    </h3>

                    <span class="booking-date">
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

            <div class="booking-content">

                <p>
                    <strong>Email:</strong>
                    {{ $booking->email }}
                </p>

                <p>
                    <strong>Phone:</strong>
                    {{ $booking->phone }}
                </p>

                <p>
                    <strong>Layanan:</strong>
                    {{ $booking->service }}
                </p>

                <p>
                    <strong>Stylist:</strong>
                    {{ $booking->stylist }}
                </p>

                <p>
                    <strong>Pembayaran:</strong>
                    {{ $booking->payment_method }}
                </p>

                {{-- BUKTI PEMBAYARAN --}}
                @if($booking->payment_proof)

                    <div class="payment-proof-box">

                        <strong>
                            Bukti Pembayaran:
                        </strong>

                        <img
                            src="{{ asset('storage/' . $booking->payment_proof) }}"
                            alt="Bukti Pembayaran"
                            class="payment-proof-img"
                        >

                    </div>

                @endif

                <p>
                    <strong>Catatan:</strong>
                    {{ $booking->note ?? '-' }}
                </p>

                {{-- LOKASI PELANGGAN --}}
                <div class="booking-location-info">

                    <h4>Lokasi Pelanggan</h4>

                    <p>
                        <strong>Alamat Lengkap:</strong>
                        {{ $booking->full_address ?? '-' }}
                    </p>

                    <p>
                        <strong>Desa/Kelurahan:</strong>
                        {{ $booking->village ?? '-' }}
                    </p>

                    <p>
                        <strong>Kecamatan:</strong>
                        {{ $booking->district ?? '-' }}
                    </p>

                    <p>
                        <strong>Kota/Kabupaten:</strong>
                        {{ $booking->city ?? '-' }}
                    </p>

                    <p>
                        <strong>Provinsi:</strong>
                        {{ $booking->province ?? '-' }}
                    </p>

                    <p>
                        <strong>Pulau:</strong>
                        {{ $booking->island ?? '-' }}
                    </p>

                    <p>
                        <strong>Koordinat:</strong>
                        {{ $booking->latitude ?? '-' }},
                        {{ $booking->longitude ?? '-' }}
                    </p>

                    <p>
                        <strong>Zona Ongkir:</strong>
                        {{ $booking->shipping_zone ?? '-' }}
                    </p>

                    <p>
                        <strong>Ongkir:</strong>
                        Rp{{ number_format($booking->shipping_cost ?? 0, 0, ',', '.') }}
                    </p>

                    <p>
                        <strong>Tiket Pesawat:</strong>
                        Rp{{ number_format($booking->flight_ticket_cost ?? 0, 0, ',', '.') }}
                    </p>

                    <p>
                        <strong>Total Biaya Lokasi:</strong>
                        Rp{{ number_format($booking->total_location_cost ?? 0, 0, ',', '.') }}
                    </p>

                    @if($booking->latitude && $booking->longitude)

                        <a
                            href="https://www.google.com/maps?q={{ $booking->latitude }},{{ $booking->longitude }}"
                            target="_blank"
                            class="btn-location-map"
                        >
                            Lihat Lokasi di Maps
                        </a>

                    @endif

                </div>

                @if($booking->reject_reason)

                    <div class="reject-box">

                        <strong>
                            Alasan Penolakan:
                        </strong>

                        <p>
                            {{ $booking->reject_reason }}
                        </p>

                    </div>

                @endif

            </div>

            {{-- ACTION BUTTON --}}
            <div class="booking-actions">

                {{-- APPROVE --}}
                <form
                    action="{{ route('admin.bookings.approve', $booking->id) }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')

                    <button class="approve-btn">
                        Setuju
                    </button>

                </form>

                {{-- PENDING --}}
                <form
                    action="{{ route('admin.bookings.pending', $booking->id) }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')

                    <button class="pending-btn">
                        Pending
                    </button>

                </form>

            </div>

            {{-- REJECT --}}
            <div class="reject-section">

                <form
                    action="{{ route('admin.bookings.reject', $booking->id) }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')

                    <textarea
                        name="reject_reason"
                        placeholder="Masukkan alasan penolakan booking..."
                        required
                    ></textarea>

                    <button class="reject-btn">
                        Tolak Booking
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

<style>

.admin-booking-container{
    max-width:1100px;
    margin:auto;
}

.booking-header{
    margin-bottom:40px;
}

.booking-header h1{
    font-size:38px;
    margin-bottom:10px;
    color:#4b4453;
    font-weight:900;
}

.booking-header p{
    color:#777;
}

.booking-item{
    background:white;
    border-radius:24px;
    padding:30px;
    margin-bottom:30px;
    border:1px solid #f1d7e1;
    box-shadow:0 10px 30px rgba(0,0,0,.04);
}

.booking-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    gap:20px;
}

.booking-top h3{
    font-size:24px;
    margin-bottom:6px;
    color:#2f2935;
}

.booking-date{
    color:#888;
    font-size:14px;
}

.booking-content p{
    margin-bottom:10px;
    color:#555;
    line-height:1.6;
}

.status{
    padding:10px 18px;
    border-radius:999px;
    font-size:13px;
    font-weight:700;
}

.status.pending{
    background:#fff4d6;
    color:#b7791f;
}

.status.approved{
    background:#dcfce7;
    color:#166534;
}

.status.rejected{
    background:#ffe2e2;
    color:#991b1b;
}

.booking-actions{
    display:flex;
    gap:15px;
    margin-top:25px;
    flex-wrap:wrap;
}

.approve-btn,
.pending-btn,
.reject-btn{
    border:none;
    padding:13px 24px;
    border-radius:14px;
    font-weight:700;
    cursor:pointer;
    transition:.25s;
}

.approve-btn{
    background:#dcfce7;
    color:#166534;
}

.pending-btn{
    background:#fff4d6;
    color:#b7791f;
}

.reject-btn{
    background:#ffe2e2;
    color:#991b1b;
    width:100%;
    margin-top:12px;
}

.approve-btn:hover,
.pending-btn:hover,
.reject-btn:hover{
    transform:translateY(-2px);
}

.reject-section{
    margin-top:25px;
}

.reject-section textarea{
    width:100%;
    min-height:110px;
    border-radius:16px;
    border:1px solid #e7d4dc;
    padding:16px;
    resize:none;
    outline:none;
}

.reject-box{
    margin-top:18px;
    background:#fff1f2;
    border-radius:14px;
    padding:16px;
}

.empty-booking{
    background:white;
    padding:40px;
    border-radius:20px;
    text-align:center;
    color:#888;
}

/* BUKTI PEMBAYARAN */

.payment-proof-box{
    margin-top:18px;
    margin-bottom:18px;
}

.payment-proof-box strong{
    display:block;
    margin-bottom:10px;
    color:#555;
}

.payment-proof-img{
    width:180px;
    height:180px;
    object-fit:cover;
    border-radius:18px;
    border:1px solid #f1d7e1;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

/* LOKASI PELANGGAN */

.booking-location-info{
    background:#fff7fb;
    border:1px solid #ffe0ea;
    border-radius:18px;
    padding:18px;
    margin-top:22px;
    margin-bottom:18px;
}

.booking-location-info h4{
    margin:0 0 14px 0;
    color:#4b4453;
    font-size:18px;
    font-weight:900;
}

.booking-location-info p{
    margin:7px 0;
    color:#4b4453;
    font-size:14px;
    line-height:1.6;
}

.btn-location-map{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    margin-top:12px;
    padding:11px 20px;
    border-radius:999px;
    background:#f7c8d8;
    color:#3f3548;
    text-decoration:none;
    font-size:14px;
    font-weight:800;
    transition:.25s ease;
    box-shadow:0 8px 18px rgba(247,200,216,.35);
}

.btn-location-map:hover{
    background:#f3a8c4;
    color:white;
    transform:translateY(-2px);
}

@media(max-width:768px){
    .booking-top{
        flex-direction:column;
        align-items:flex-start;
    }

    .booking-item{
        padding:22px;
    }

    .booking-actions{
        flex-direction:column;
    }

    .approve-btn,
    .pending-btn{
        width:100%;
    }
}

</style>

@endsection
