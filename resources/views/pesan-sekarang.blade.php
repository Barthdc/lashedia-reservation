@extends('layouts.app')

@section('content')

<style>
    body{
        background:#f5f5f7;
        font-family:'DM Sans', sans-serif;
    }

    .booking-wrapper{
        min-height:calc(100vh - 80px);
        display:flex;
        align-items:center;
        justify-content:center;
        padding:60px 20px;
    }

    .booking-card{
        width:100%;
        max-width:520px;
        background:white;
        border-radius:16px;
        padding:40px;
        box-shadow:0 10px 35px rgba(0,0,0,0.05);
        border:1px solid #e5e7eb;
    }

    .booking-title{
        text-align:center;
        margin-bottom:10px;
        font-size:42px;
        font-family:'Playfair Display', serif;
        color:#1a1a2e;
    }

    .booking-subtitle{
        text-align:center;
        color:#6b7280;
        font-size:14px;
        line-height:1.7;
        margin-bottom:35px;
    }

    .form-group{
        margin-bottom:22px;
    }

    .form-group label{
        display:block;
        margin-bottom:8px;
        font-size:14px;
        color:#1a1a2e;
        font-weight:500;
    }

    .form-control{
        width:100%;
        border:1px solid #d1d5db;
        background:#f9fafb;
        border-radius:10px;
        padding:14px 16px;
        font-size:14px;
        outline:none;
        transition:.2s;
    }

    .form-control:focus{
        border-color:#6c5ce7;
        background:white;
        box-shadow:0 0 0 4px rgba(108,92,231,.12);
    }

    textarea.form-control{
        resize:vertical;
        min-height:110px;
    }

    .btn-booking{
        width:100%;
        border:none;
        background:#6c5ce7;
        color:white;
        padding:15px;
        border-radius:10px;
        font-size:15px;
        font-weight:600;
        cursor:pointer;
        transition:.2s;
    }

    .btn-booking:hover{
        background:#5a4ed1;
        transform:translateY(-1px);
    }

    .payment-info{
        background:#f3f4f6;
        padding:14px;
        border-radius:10px;
        margin-bottom:20px;
        font-size:14px;
        color:#4b5563;
    }

    .success-alert{
        background:#dcfce7;
        color:#166534;
        padding:14px;
        border-radius:10px;
        margin-bottom:20px;
        font-size:14px;
    }

    .google-calendar-btn{
        display:block;
        text-align:center;
        background:#4285F4;
        color:white;
        padding:14px;
        border-radius:10px;
        text-decoration:none;
        font-weight:600;
        margin-bottom:20px;
    }

    .google-calendar-btn:hover{
        background:#3367d6;
        color:white;
    }

    @media(max-width:768px){
        .booking-card{
            padding:30px 22px;
        }

        .booking-title{
            font-size:32px;
        }
    }
</style>

<div class="booking-wrapper">

    <div class="booking-card">

        <h1 class="booking-title">
            Buat Janji Temu
        </h1>

        <p class="booking-subtitle">
            Isi formulir di bawah ini untuk memesan penata rias Anda.
            Kami akan segera menghubungi Anda.
        </p>

        {{-- GOOGLE CALENDAR ADMIN --}}
        @if(Auth::check() && Auth::user()->role === 'admin')

            @if(isset($googleCalendarConnected) && $googleCalendarConnected)

                <div class="success-alert">
                    Google Calendar sudah terhubung. Setiap booking baru akan otomatis masuk ke kalender.
                </div>

            @else

                <div class="payment-info">
                    Google Calendar belum terhubung. Klik tombol di bawah ini agar booking otomatis masuk ke Google Calendar admin.
                </div>

                <a
                    href="{{ route('google.auth') }}"
                    class="google-calendar-btn"
                >
                    Hubungkan Google Calendar
                </a>

            @endif

        @endif

        {{-- ALERT SUCCESS --}}
        @if(session('success'))

            <div class="success-alert">
                {{ session('success') }}
            </div>

        @endif

        <form
            action="{{ route('pesan.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf

            {{-- TANGGAL --}}
            <div class="form-group">

                <label>Tanggal Booking</label>

                <input
                    type="date"
                    class="form-control"
                    name="date"
                    required
                >

            </div>

            {{-- JAM --}}
            <div class="form-group">

                <label>Jam Booking</label>

                <input
                    type="time"
                    class="form-control"
                    name="time"
                    required
                >

            </div>

            {{-- PENATA RIAS --}}
            <div class="form-group">

                <label>Penata Rias</label>

                <select
                    class="form-control"
                    name="stylist"
                >

                    <option value="">
                        Pilih Penata Rias
                    </option>

                    <option value="Bernike Ledibeth">
                        Bernike Ledibeth
                    </option>

                    <option value="Sally">
                        Sally
                    </option>

                    <option value="Kiki">
                        Kiki
                    </option>

                </select>

            </div>

            {{-- LAYANAN --}}
            <div class="form-group">

                <label>Layanan</label>

                <select
                    class="form-control"
                    name="service"
                    required
                >

                    <option value="">
                        Pilih sebuah layanan
                    </option>

                    <option value="Wedding Makeup">
                        Wedding Makeup
                    </option>

                    <option value="Wisuda Makeup">
                        Wisuda Makeup
                    </option>

                    <option value="Eyelash Extension">
                        Eyelash Extension
                    </option>

                    <option value="Nail Art">
                        Nail Art
                    </option>

                    <option value="Hair Do">
                        Hair Do
                    </option>

                </select>

            </div>

            {{-- NAMA --}}
            <div class="form-group">

                <label>Nama Lengkap</label>

                <input
                    type="text"
                    class="form-control"
                    placeholder="Jane Doe"
                    name="name"
                    required
                >

            </div>

            {{-- EMAIL --}}
            <div class="form-group">

                <label>Alamat Email</label>

                <input
                    type="email"
                    class="form-control"
                    placeholder="anda@contoh.com"
                    name="email"
                    required
                >

            </div>

            {{-- PHONE --}}
            <div class="form-group">

                <label>Nomor Telepon (WhatsApp)</label>

                <input
                    type="text"
                    class="form-control"
                    placeholder="+62 812-3456-7890"
                    name="phone"
                    required
                >

            </div>

            {{-- PEMBAYARAN --}}
            <div class="form-group">

                <label>Metode Pembayaran</label>

                <select
                    class="form-control"
                    name="payment_method"
                    id="metodePembayaran"
                    onchange="toggleBuktiPembayaran()"
                >

                    <option value="">
                        Pilih metode pembayaran
                    </option>

                    <option value="cash">
                        Cash
                    </option>

                    <option value="transfer">
                        Transfer Bank
                    </option>

                    <option value="ewallet">
                        E-Wallet
                    </option>

                </select>

            </div>

            {{-- INFO CASH --}}
            <div
                id="cashInfo"
                style="display:none;"
                class="payment-info"
            >
                Anda memilih pembayaran cash.
                Pembayaran dilakukan saat hari appointment.
            </div>

            {{-- BUKTI PEMBAYARAN --}}
            <div
                class="form-group"
                id="paymentProofBox"
                style="display:none;"
            >

                <label>Upload Bukti Pembayaran</label>

                <input
                    type="file"
                    name="payment_proof"
                    class="form-control"
                    accept="image/*"
                >

            </div>

            {{-- CATATAN --}}
            <div class="form-group">

                <label>Catatan Tambahan</label>

                <textarea
                    class="form-control"
                    name="note"
                    placeholder="Tambahkan catatan booking..."
                ></textarea>

            </div>

            <button type="submit" class="btn-booking">
                Kirim Permintaan Pemesanan
            </button>

        </form>

    </div>

</div>

<script>
    function toggleBuktiPembayaran(){

        const metode = document.getElementById('metodePembayaran').value;
        const bukti = document.getElementById('paymentProofBox');
        const cashInfo = document.getElementById('cashInfo');

        if(metode === 'cash'){

            bukti.style.display = 'none';
            cashInfo.style.display = 'block';

        }else if(metode === ''){

            bukti.style.display = 'none';
            cashInfo.style.display = 'none';

        }else{

            bukti.style.display = 'block';
            cashInfo.style.display = 'none';

        }
    }

    window.onload = toggleBuktiPembayaran;
</script>

@endsection
