@extends('layouts.admin')

@section('content')

<div class="admin-booking-container">

    <div class="booking-header">
        <h1>Daftar Booking Customer</h1>
        <p>Kelola semua pesanan customer Lashedia</p>
    </div>

    @if(session('success'))
        <div class="success-alert">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @forelse($bookings as $booking)

        <div class="booking-item">

            {{-- HEADER BOOKING --}}
            <div class="booking-top">

                <div>
                    <h3>{{ $booking->name }}</h3>

                    <span class="booking-date">
                        {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}
                        -
                        {{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}
                    </span>
                </div>

                <div>
                    @if($booking->status == 'Approved')
                        <span class="status approved">Approved</span>
                    @elseif($booking->status == 'Rejected')
                        <span class="status rejected">Rejected</span>
                    @else
                        <span class="status pending">Pending</span>
                    @endif
                </div>

            </div>

            {{-- CONTENT BOOKING --}}
            <div class="booking-content">

                <div class="booking-detail-grid">

                    <p><strong>Email:</strong> {{ $booking->email }}</p>
                    <p><strong>Phone:</strong> {{ $booking->phone }}</p>
                    <p><strong>Layanan:</strong> {{ $booking->service }}</p>
                    <p><strong>Stylist:</strong> {{ $booking->stylist }}</p>
                    <p><strong>Pembayaran:</strong> {{ $booking->payment_method ?? '-' }}</p>
                    <p><strong>Status:</strong> {{ $booking->status ?? 'Pending' }}</p>

                </div>

                {{-- BUKTI PEMBAYARAN --}}
                @if($booking->payment_proof)
                    <div class="payment-proof-box">
                        <strong>Bukti Pembayaran:</strong>

                        <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank">
                            <img
                                src="{{ asset('storage/' . $booking->payment_proof) }}"
                                alt="Bukti Pembayaran"
                                class="payment-proof-img"
                            >
                        </a>
                    </div>
                @endif

                {{-- CATATAN --}}
                <p>
                    <strong>Catatan:</strong>
                    {{ $booking->note ?? '-' }}
                </p>

                {{-- LOKASI DAN TRANSPORT --}}
                <div class="booking-location-info">

                    <h4>Lokasi & Transport Pelanggan</h4>

                    <p><strong>Alamat / Titik Lokasi:</strong> {{ $booking->full_address ?? '-' }}</p>
                    <p><strong>Koordinat Pelanggan:</strong> {{ $booking->latitude ?? '-' }}, {{ $booking->longitude ?? '-' }}</p>
                    <p><strong>Koordinat MUA:</strong> {{ $booking->mua_latitude ?? '-' }}, {{ $booking->mua_longitude ?? '-' }}</p>
                    <p><strong>Jarak dari Lokasi MUA:</strong> {{ $booking->distance_km ?? 0 }} km</p>
                    <p><strong>Biaya Transport:</strong> Rp{{ number_format($booking->transport_cost ?? 0, 0, ',', '.') }}</p>
                    <p><strong>Keterangan:</strong> {{ $booking->transport_note ?? '-' }}</p>

                    @if($booking->latitude && $booking->longitude)
                        <a
                            href="https://www.google.com/maps?q={{ $booking->latitude }},{{ $booking->longitude }}"
                            target="_blank"
                            class="btn-location-map"
                        >
                            Lihat Lokasi Pelanggan
                        </a>
                    @endif

                </div>

                {{-- INVOICE OTOMATIS --}}
                @if($booking->invoice_number)

                    <div class="booking-invoice-box">

                        <div class="invoice-header">
                            <div>
                                <h4>Invoice Otomatis</h4>
                                <span>{{ $booking->invoice_number }}</span>
                            </div>

                            @if($booking->invoice_sent_at)
                                <span class="invoice-badge sent">Email Terkirim</span>
                            @else
                                <span class="invoice-badge unsent">Belum Terkirim</span>
                            @endif
                        </div>

                        <div class="invoice-detail-grid">

                            <p><strong>No. Invoice:</strong> {{ $booking->invoice_number }}</p>

                            <p>
                                <strong>Tanggal Invoice:</strong>
                                {{ $booking->invoice_date ? \Carbon\Carbon::parse($booking->invoice_date)->format('d M Y H:i') : '-' }}
                            </p>

                            <p>
                                <strong>Biaya Layanan:</strong>
                                Rp{{ number_format($booking->invoice_subtotal ?? 0, 0, ',', '.') }}
                            </p>

                            <p>
                                <strong>Biaya Transport:</strong>
                                Rp{{ number_format($booking->invoice_transport ?? 0, 0, ',', '.') }}
                            </p>

                            <p class="invoice-total">
                                <strong>Total Invoice:</strong>
                                Rp{{ number_format($booking->invoice_total ?? 0, 0, ',', '.') }}
                            </p>

                            <p>
                                <strong>Status Email Invoice:</strong>
                                @if($booking->invoice_sent_at)
                                    Terkirim pada {{ \Carbon\Carbon::parse($booking->invoice_sent_at)->format('d M Y H:i') }}
                                @else
                                    Belum terkirim
                                @endif
                            </p>

                        </div>

                    </div>

                @else

                    <div class="booking-invoice-box invoice-empty">
                        <h4>Invoice Otomatis</h4>
                        <p>Invoice akan dibuat otomatis setelah admin menyetujui booking.</p>
                    </div>

                @endif

                {{-- ALASAN PENOLAKAN --}}
                @if($booking->reject_reason)
                    <div class="reject-box">
                        <strong>Alasan Penolakan:</strong>
                        <p>{{ $booking->reject_reason }}</p>
                    </div>
                @endif

            </div>

            {{-- ACTION BUTTON --}}
            <div class="booking-actions">

                {{-- APPROVE --}}
                <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button class="approve-btn" type="submit">
                        Setuju & Kirim Invoice
                    </button>
                </form>

                {{-- PENDING --}}
                <form action="{{ route('admin.bookings.pending', $booking->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button class="pending-btn" type="submit">
                        Pending
                    </button>
                </form>

                {{-- DELETE --}}
                <form
                    action="{{ route('admin.bookings.destroy', $booking->id) }}"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus booking ini?')"
                >
                    @csrf
                    @method('DELETE')

                    <button class="delete-btn" type="submit">
                        Hapus
                    </button>
                </form>

            </div>

            {{-- REJECT --}}
            <div class="reject-section">
                <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <textarea
                        name="reject_reason"
                        placeholder="Masukkan alasan penolakan booking..."
                        required
                    ></textarea>

                    <button class="reject-btn" type="submit">
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

    .success-alert{
        background:#dcfce7;
        color:#166534;
        padding:16px 18px;
        border-radius:16px;
        margin-bottom:22px;
        font-size:14px;
        font-weight:700;
        line-height:1.6;
    }

    .error-alert{
        background:#fee2e2;
        color:#991b1b;
        padding:16px 18px;
        border-radius:16px;
        margin-bottom:22px;
        font-size:14px;
        font-weight:700;
        line-height:1.6;
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

    .booking-detail-grid,
    .invoice-detail-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));
        gap:8px 22px;
    }

    .status{
        padding:10px 18px;
        border-radius:999px;
        font-size:13px;
        font-weight:800;
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
    .reject-btn,
    .delete-btn{
        border:none;
        padding:13px 24px;
        border-radius:14px;
        font-weight:800;
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

    .delete-btn{
        background:#f3f4f6;
        color:#374151;
    }

    .reject-btn{
        background:#ffe2e2;
        color:#991b1b;
        width:100%;
        margin-top:12px;
    }

    .approve-btn:hover,
    .pending-btn:hover,
    .reject-btn:hover,
    .delete-btn:hover{
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

    .reject-section textarea:focus{
        border-color:#f3a8c4;
        box-shadow:0 0 0 4px rgba(247,200,216,.22);
    }

    .reject-box{
        margin-top:18px;
        background:#fff1f2;
        border-radius:14px;
        padding:16px;
        color:#991b1b;
    }

    .reject-box p{
        margin:8px 0 0;
        color:#991b1b;
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

    /* INVOICE */
    .booking-invoice-box{
        background:#fff7fb;
        border:2px solid #f7c8d8;
        border-radius:18px;
        padding:18px;
        margin-top:20px;
        margin-bottom:18px;
        color:#4b4453;
    }

    .booking-invoice-box h4{
        margin:0 0 4px;
        color:#1f1f1f;
        font-size:18px;
        font-weight:900;
    }

    .booking-invoice-box p{
        margin:7px 0;
        font-size:14px;
        line-height:1.6;
    }

    .booking-invoice-box strong{
        color:#1f1f1f;
    }

    .invoice-empty{
        background:#f9fafb;
        border:1px dashed #d1d5db;
    }

    .invoice-header{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
        margin-bottom:16px;
    }

    .invoice-header span{
        color:#6b7280;
        font-size:13px;
        font-weight:700;
    }

    .invoice-badge{
        padding:8px 14px;
        border-radius:999px;
        font-size:12px !important;
        font-weight:900 !important;
        white-space:nowrap;
    }

    .invoice-badge.sent{
        background:#dcfce7;
        color:#166534;
    }

    .invoice-badge.unsent{
        background:#fff4d6;
        color:#b7791f;
    }

    .invoice-total{
        font-size:16px !important;
        font-weight:900;
        color:#1f1f1f !important;
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
        .pending-btn,
        .delete-btn{
            width:100%;
        }

        .invoice-header{
            flex-direction:column;
        }
    }
</style>

@endsection
