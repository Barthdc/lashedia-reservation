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
        max-width:620px;
        background:white;
        border-radius:18px;
        padding:42px;
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
        font-weight:600;
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
        border-color:#f3a8c4;
        background:white;
        box-shadow:0 0 0 4px rgba(247,200,216,.25);
    }

    textarea.form-control{
        resize:vertical;
        min-height:110px;
    }

    .btn-booking{
        width:100%;
        border:none;
        background:#f7c8d8;
        color:#3f3548;
        padding:15px;
        border-radius:999px;
        font-size:15px;
        font-weight:800;
        cursor:pointer;
        transition:.25s ease;
        box-shadow:0 10px 24px rgba(247,200,216,.35);
    }

    .btn-booking:hover{
        background:#f3a8c4;
        color:white;
        transform:translateY(-2px);
        box-shadow:0 14px 30px rgba(247,200,216,.48);
    }

    .payment-info{
        background:#fff7fb;
        border:1px solid #ffe0ea;
        padding:14px;
        border-radius:12px;
        margin-bottom:20px;
        font-size:14px;
        color:#4b4453;
        line-height:1.7;
    }

    .success-alert{
        background:#dcfce7;
        color:#166534;
        padding:14px;
        border-radius:12px;
        margin-bottom:20px;
        font-size:14px;
        line-height:1.6;
    }

    .error-alert{
        background:#fee2e2;
        color:#991b1b;
        padding:14px;
        border-radius:12px;
        margin-bottom:20px;
        font-size:14px;
        line-height:1.6;
    }

    .schedule-warning{
        display:none;
        background:#fff4cc;
        color:#8a6500;
        padding:14px;
        border-radius:12px;
        margin-bottom:20px;
        font-size:14px;
        line-height:1.6;
    }

    .google-calendar-btn{
        display:block;
        text-align:center;
        background:#4285F4;
        color:white;
        padding:14px;
        border-radius:999px;
        text-decoration:none;
        font-weight:800;
        margin-bottom:20px;
        transition:.25s ease;
    }

    .google-calendar-btn:hover{
        background:#3367d6;
        color:white;
        transform:translateY(-2px);
    }

    .section-title{
        margin:30px 0 18px;
        padding-bottom:10px;
        border-bottom:1px solid #ffe0ea;
        color:#4b4453;
        font-size:22px;
        font-weight:900;
        font-family:'Playfair Display', serif;
    }

    .location-button{
        margin-top:12px;
        background:#4b4453;
        color:white;
    }

    .location-button:hover{
        background:#2f2935;
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

        {{-- ALERT ERROR --}}
        @if($errors->any())

            <div class="error-alert">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>

        @endif

        <div id="scheduleWarning" class="schedule-warning"></div>

        <form
            action="{{ route('pesan.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="bookingForm"
        >

            @csrf

            <div class="section-title">
                Data Booking
            </div>

            {{-- TANGGAL --}}
            <div class="form-group">

                <label>Tanggal Booking</label>

                <input
                    type="date"
                    class="form-control"
                    name="date"
                    id="bookingDate"
                    value="{{ old('date') }}"
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
                    id="bookingTime"
                    value="{{ old('time') }}"
                    required
                >

            </div>

            {{-- PENATA RIAS --}}
            <div class="form-group">

                <label>Penata Rias</label>

                <select
                    class="form-control"
                    name="stylist"
                    id="bookingStylist"
                    required
                >

                    <option value="">
                        Pilih Penata Rias
                    </option>

                    <option value="Bernike Ledibeth" {{ old('stylist') === 'Bernike Ledibeth' ? 'selected' : '' }}>
                        Bernike Ledibeth
                    </option>

                    <option value="Sally" {{ old('stylist') === 'Sally' ? 'selected' : '' }}>
                        Sally
                    </option>

                    <option value="Kiki" {{ old('stylist') === 'Kiki' ? 'selected' : '' }}>
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
                    id="bookingService"
                    required
                >

                    <option value="">
                        Pilih sebuah layanan
                    </option>

                    <option value="Wedding Makeup" {{ old('service') === 'Wedding Makeup' ? 'selected' : '' }}>
                        Wedding Makeup
                    </option>

                    <option value="Wisuda Makeup" {{ old('service') === 'Wisuda Makeup' ? 'selected' : '' }}>
                        Wisuda Makeup
                    </option>

                    <option value="Eyelash Extension" {{ old('service') === 'Eyelash Extension' ? 'selected' : '' }}>
                        Eyelash Extension
                    </option>

                    <option value="Nail Art" {{ old('service') === 'Nail Art' ? 'selected' : '' }}>
                        Nail Art
                    </option>

                    <option value="Hair Do" {{ old('service') === 'Hair Do' ? 'selected' : '' }}>
                        Hair Do
                    </option>

                </select>

            </div>

            <div class="section-title">
                Data Pelanggan
            </div>

            {{-- NAMA --}}
            <div class="form-group">

                <label>Nama Lengkap</label>

                <input
                    type="text"
                    class="form-control"
                    placeholder="Jane Doe"
                    name="name"
                    value="{{ old('name') }}"
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
                    value="{{ old('email') }}"
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
                    value="{{ old('phone') }}"
                    required
                >

            </div>

            <div class="section-title">
                Mapping Lokasi Pelanggan
            </div>

            {{-- ALAMAT --}}
            <div class="form-group">

                <label>Alamat Lengkap</label>

                <textarea
                    class="form-control"
                    name="full_address"
                    placeholder="Masukkan alamat lengkap pelanggan..."
                    required
                >{{ old('full_address') }}</textarea>

            </div>

            {{-- DESA --}}
            <div class="form-group">

                <label>Desa / Kelurahan</label>

                <input
                    type="text"
                    class="form-control"
                    name="village"
                    placeholder="Contoh: Sukamantri"
                    value="{{ old('village') }}"
                    required
                >

            </div>

            {{-- KECAMATAN --}}
            <div class="form-group">

                <label>Kecamatan</label>

                <input
                    type="text"
                    class="form-control"
                    name="district"
                    placeholder="Contoh: Pasar Kemis"
                    value="{{ old('district') }}"
                    required
                >

            </div>

            {{-- KOTA --}}
            <div class="form-group">

                <label>Kota / Kabupaten</label>

                <input
                    type="text"
                    class="form-control"
                    name="city"
                    id="cityInput"
                    placeholder="Contoh: Kabupaten Tangerang"
                    value="{{ old('city') }}"
                    required
                >

            </div>

            {{-- PROVINSI --}}
            <div class="form-group">

                <label>Provinsi</label>

                <input
                    type="text"
                    class="form-control"
                    name="province"
                    id="provinceInput"
                    placeholder="Contoh: Banten"
                    value="{{ old('province') }}"
                    required
                >

            </div>

            {{-- PULAU --}}
            <div class="form-group">

                <label>Pulau</label>

                <select
                    class="form-control"
                    name="island"
                    id="islandInput"
                    required
                >
                    <option value="">Pilih Pulau</option>

                    <option value="Jawa" {{ old('island') === 'Jawa' ? 'selected' : '' }}>
                        Jawa
                    </option>

                    <option value="Sumatera" {{ old('island') === 'Sumatera' ? 'selected' : '' }}>
                        Sumatera
                    </option>

                    <option value="Kalimantan" {{ old('island') === 'Kalimantan' ? 'selected' : '' }}>
                        Kalimantan
                    </option>

                    <option value="Sulawesi" {{ old('island') === 'Sulawesi' ? 'selected' : '' }}>
                        Sulawesi
                    </option>

                    <option value="Bali" {{ old('island') === 'Bali' ? 'selected' : '' }}>
                        Bali
                    </option>

                    <option value="Nusa Tenggara" {{ old('island') === 'Nusa Tenggara' ? 'selected' : '' }}>
                        Nusa Tenggara
                    </option>

                    <option value="Maluku" {{ old('island') === 'Maluku' ? 'selected' : '' }}>
                        Maluku
                    </option>

                    <option value="Papua" {{ old('island') === 'Papua' ? 'selected' : '' }}>
                        Papua
                    </option>

                </select>

            </div>

            {{-- TIKET PESAWAT --}}
            <div
                class="form-group"
                id="flightTicketBox"
                style="display:none;"
            >

                <label>Estimasi Tiket Pesawat</label>

                <input
                    type="number"
                    class="form-control"
                    name="flight_ticket_cost"
                    id="flightTicketInput"
                    placeholder="Contoh: 1500000"
                    value="{{ old('flight_ticket_cost', 0) }}"
                >

            </div>

            {{-- KOORDINAT --}}
            <div class="form-group">

                <label>Koordinat Lokasi</label>

                <input
                    type="text"
                    class="form-control"
                    name="latitude"
                    id="latitudeInput"
                    placeholder="Latitude"
                    value="{{ old('latitude') }}"
                    readonly
                    style="margin-bottom:10px;"
                >

                <input
                    type="text"
                    class="form-control"
                    name="longitude"
                    id="longitudeInput"
                    placeholder="Longitude"
                    value="{{ old('longitude') }}"
                    readonly
                >

                <button
                    type="button"
                    class="btn-booking location-button"
                    onclick="getCustomerLocation()"
                >
                    Ambil Titik Lokasi Saya
                </button>

            </div>

            {{-- INFO ONGKIR --}}
            <div class="payment-info" id="shippingInfo">
                Ongkir akan dihitung otomatis berdasarkan kota, provinsi, dan pulau pelanggan.
            </div>

            <div class="section-title">
                Pembayaran
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

                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>
                        Cash
                    </option>

                    <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>
                        Transfer Bank
                    </option>

                    <option value="ewallet" {{ old('payment_method') === 'ewallet' ? 'selected' : '' }}>
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
                >{{ old('note') }}</textarea>

            </div>

            <button type="submit" class="btn-booking">
                Kirim Permintaan Pemesanan
            </button>

        </form>

    </div>

</div>

<script>
    const blockedWeddingDates = @json($blockedWeddingDates ?? []);
    const timeBlocks = @json($timeBlocks ?? []);

    const bookingForm = document.getElementById('bookingForm');
    const bookingDate = document.getElementById('bookingDate');
    const bookingTime = document.getElementById('bookingTime');
    const bookingService = document.getElementById('bookingService');
    const bookingStylist = document.getElementById('bookingStylist');
    const scheduleWarning = document.getElementById('scheduleWarning');

    const cityInput = document.getElementById('cityInput');
    const provinceInput = document.getElementById('provinceInput');
    const islandInput = document.getElementById('islandInput');
    const flightTicketBox = document.getElementById('flightTicketBox');
    const flightTicketInput = document.getElementById('flightTicketInput');
    const shippingInfo = document.getElementById('shippingInfo');

    function timeToMinutes(time){
        if(!time){
            return null;
        }

        const parts = time.split(':');

        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    function showScheduleWarning(message){
        scheduleWarning.innerHTML = message;
        scheduleWarning.style.display = 'block';
    }

    function hideScheduleWarning(){
        scheduleWarning.innerHTML = '';
        scheduleWarning.style.display = 'none';
    }

    function validateSchedule(){
        hideScheduleWarning();

        const selectedDate = bookingDate.value;
        const selectedTime = bookingTime.value;
        const selectedStylist = bookingStylist.value;

        if(!selectedDate || !selectedStylist){
            return true;
        }

        const stylistBlockedWeddingDates = blockedWeddingDates[selectedStylist] ?? [];

        if(stylistBlockedWeddingDates.includes(selectedDate)){
            showScheduleWarning(
                'Tanggal ini sudah diblokir untuk stylist ' +
                selectedStylist +
                ' karena terdapat booking Wedding Makeup. Silakan pilih stylist atau tanggal lain.'
            );

            return false;
        }

        if(!selectedTime){
            return true;
        }

        const selectedStart = timeToMinutes(selectedTime);
        const selectedEnd = selectedStart + 180;

        const stylistBlocks = timeBlocks[selectedStylist] ?? [];
        const dateBlocks = stylistBlocks[selectedDate] ?? [];

        for(const block of dateBlocks){
            const blockStart = timeToMinutes(block.start);
            const blockEnd = timeToMinutes(block.end);

            const isOverlapping = selectedStart < blockEnd && selectedEnd > blockStart;

            if(isOverlapping){
                showScheduleWarning(
                    'Jam ini tidak tersedia untuk stylist ' +
                    selectedStylist +
                    '. Sudah ada booking dari jam ' +
                    block.start + ' sampai ' + block.end +
                    '. Silakan pilih jam atau stylist lain.'
                );

                return false;
            }
        }

        return true;
    }

    function formatRupiah(number){
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    function calculateShippingCost(){
        const city = (cityInput.value || '').toLowerCase().trim();
        const province = (provinceInput.value || '').toLowerCase().trim();
        const island = (islandInput.value || '').toLowerCase().trim();
        const flightTicket = parseInt(flightTicketInput.value || 0);

        const jabodetabekCities = [
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
            'kabupaten bekasi'
        ];

        let zone = '';
        let shippingCost = 0;
        let totalCost = 0;

        if(island && island !== 'jawa'){
            zone = 'Luar Pulau';
            shippingCost = 800000;
            totalCost = shippingCost + flightTicket;

            flightTicketBox.style.display = 'block';
        }else{
            flightTicketBox.style.display = 'none';

            if(province && province !== 'banten'){
                zone = 'Luar Provinsi Banten';
                shippingCost = 400000;
            }else if(jabodetabekCities.includes(city)){
                zone = 'Jabodetabek';
                shippingCost = 100000;
            }else{
                zone = 'Luar Jabodetabek';
                shippingCost = 200000;
            }

            totalCost = shippingCost;
        }

        shippingInfo.innerHTML =
            '<strong>Zona:</strong> ' + zone + '<br>' +
            '<strong>Ongkir:</strong> ' + formatRupiah(shippingCost) + '<br>' +
            '<strong>Tiket Pesawat:</strong> ' + formatRupiah(flightTicket) + '<br>' +
            '<strong>Total Biaya Lokasi:</strong> ' + formatRupiah(totalCost);
    }

    function getCustomerLocation(){
        if(!navigator.geolocation){
            alert('Browser tidak mendukung fitur lokasi.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position){
                document.getElementById('latitudeInput').value =
                    position.coords.latitude.toFixed(7);

                document.getElementById('longitudeInput').value =
                    position.coords.longitude.toFixed(7);

                alert('Titik lokasi berhasil diambil.');
            },
            function(){
                alert('Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.');
            }
        );
    }

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

    bookingDate.addEventListener('change', validateSchedule);
    bookingTime.addEventListener('change', validateSchedule);
    bookingService.addEventListener('change', validateSchedule);
    bookingStylist.addEventListener('change', validateSchedule);

    cityInput.addEventListener('input', calculateShippingCost);
    provinceInput.addEventListener('input', calculateShippingCost);
    islandInput.addEventListener('change', calculateShippingCost);
    flightTicketInput.addEventListener('input', calculateShippingCost);

    bookingForm.addEventListener('submit', function(e){
        const valid = validateSchedule();

        if(!valid){
            e.preventDefault();
        }
    });

    window.onload = function(){
        toggleBuktiPembayaran();
        validateSchedule();
        calculateShippingCost();
    };
</script>

@endsection
