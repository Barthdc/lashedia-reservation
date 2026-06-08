@extends('layouts.app')

@section('content')

{{-- LEAFLET CSS --}}
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

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
        max-width:720px;
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

    .btn-small{
        width:auto;
        padding:13px 22px;
        white-space:nowrap;
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

    .leaflet-map-box{
        width:100%;
        height:380px;
        border-radius:18px;
        overflow:hidden;
        margin-bottom:18px;
        border:2px solid #f7c8d8;
        box-shadow:0 12px 28px rgba(247,200,216,.18);
    }

    .location-info-box,
    .location-result-box{
        background:#fff7fb;
        border:1px solid #ffe0ea;
        color:#4b4453;
        padding:14px 16px;
        border-radius:14px;
        margin-bottom:18px;
        font-size:14px;
        line-height:1.7;
    }

    .location-search-row{
        display:flex;
        gap:10px;
        align-items:center;
        margin-bottom:14px;
    }

    .location-search-row .form-control{
        flex:1;
    }

    .location-button{
        margin-bottom:18px;
        background:#4b4453;
        color:white;
    }

    .location-button:hover{
        background:#2f2935;
        color:white;
    }

    .map-help{
        font-size:13px;
        color:#6b7280;
        line-height:1.7;
        margin-bottom:14px;
    }

    .transport-price-box{
        background:#f7c8d8;
        border:2px solid #1f1f1f;
        border-radius:18px;
        padding:18px;
        margin-bottom:22px;
        color:#1f1f1f;
        line-height:1.8;
        font-size:15px;
        font-weight:700;
        box-shadow:0 12px 28px rgba(247,200,216,.28);
    }

    .transport-price-box strong{
        font-weight:900;
    }

    @media(max-width:768px){
        .booking-card{
            padding:30px 22px;
        }

        .booking-title{
            font-size:32px;
        }

        .leaflet-map-box{
            height:320px;
        }

        .location-search-row{
            flex-direction:column;
        }

        .btn-small{
            width:100%;
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
            Lokasi pelanggan dapat diketik manual atau dipilih langsung melalui peta.
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
                    <option value="">Pilih Penata Rias</option>

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
                    <option value="">Pilih sebuah layanan</option>

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

            <div class="location-info-box">
                Ketik alamat pada kolom pencarian lalu klik <strong>Cari Lokasi</strong>.
                Map akan otomatis membaca alamat tersebut, memindahkan marker ke titik lokasi, lalu menampilkan jarak dan harga transport untuk user.
            </div>

            {{-- INPUT MANUAL ALAMAT --}}
            <div class="form-group">
                <label>Input Alamat / Nama Lokasi</label>

                <div class="location-search-row">
                    <input
                        type="text"
                        class="form-control"
                        id="manualAddressInput"
                        placeholder="Contoh: Pasar Kemis, Kabupaten Tangerang"
                        value="{{ old('full_address') }}"
                    >

                    <button
                        type="button"
                        class="btn-booking btn-small"
                        onclick="searchManualAddress()"
                    >
                        Cari Lokasi
                    </button>
                </div>

                <div class="map-help">
                    Setelah lokasi ditemukan, marker pada peta akan berpindah otomatis. Anda juga tetap bisa klik peta atau drag marker untuk memperbaiki titik.
                </div>
            </div>

            <div id="map" class="leaflet-map-box"></div>

            <button
                type="button"
                class="btn-booking location-button"
                onclick="useCurrentLocation()"
            >
                Gunakan Lokasi Saya
            </button>

            <div class="location-result-box" id="locationResult">
                Lokasi belum dipilih.
            </div>

            <div class="transport-price-box" id="transportInfo">
                Harga transport akan tampil setelah lokasi dipilih atau alamat dicari.
            </div>

            <input
                type="hidden"
                name="full_address"
                id="fullAddressInput"
                value="{{ old('full_address') }}"
            >

            <input
                type="hidden"
                name="latitude"
                id="latitudeInput"
                value="{{ old('latitude') }}"
            >

            <input
                type="hidden"
                name="longitude"
                id="longitudeInput"
                value="{{ old('longitude') }}"
            >

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
                    <option value="">Pilih metode pembayaran</option>

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

{{-- LEAFLET JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const blockedWeddingDates = @json($blockedWeddingDates ?? []);
    const timeBlocks = @json($timeBlocks ?? []);

    const bookingForm = document.getElementById('bookingForm');
    const bookingDate = document.getElementById('bookingDate');
    const bookingTime = document.getElementById('bookingTime');
    const bookingService = document.getElementById('bookingService');
    const bookingStylist = document.getElementById('bookingStylist');
    const scheduleWarning = document.getElementById('scheduleWarning');

    const manualAddressInput = document.getElementById('manualAddressInput');
    const locationResult = document.getElementById('locationResult');
    const transportInfo = document.getElementById('transportInfo');

    const fullAddressInput = document.getElementById('fullAddressInput');
    const latitudeInput = document.getElementById('latitudeInput');
    const longitudeInput = document.getElementById('longitudeInput');

    const muaLat = {{ env('MUA_LATITUDE', -6.170170) }};
    const muaLng = {{ env('MUA_LONGITUDE', 106.640300) }};

    let map;
    let customerMarker;

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

    function initMap(){
        map = L.map('map').setView([muaLat, muaLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom:19,
            attribution:'&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([muaLat, muaLng])
            .addTo(map)
            .bindPopup('Lokasi MUA Lashedia')
            .openPopup();

        customerMarker = L.marker([muaLat, muaLng], {
            draggable:true
        }).addTo(map);

        customerMarker.bindPopup('Geser marker ini ke lokasi pelanggan.');

        customerMarker.on('dragend', function(){
            const position = customerMarker.getLatLng();
            setCustomerLocation(position.lat, position.lng, null, true);
        });

        map.on('click', function(e){
            customerMarker.setLatLng(e.latlng);
            setCustomerLocation(e.latlng.lat, e.latlng.lng, null, true);
        });

        if(latitudeInput.value && longitudeInput.value){
            const oldLat = parseFloat(latitudeInput.value);
            const oldLng = parseFloat(longitudeInput.value);

            map.setView([oldLat, oldLng], 16);
            customerMarker.setLatLng([oldLat, oldLng]);
            setCustomerLocation(oldLat, oldLng, fullAddressInput.value, false);
        }
    }

    function searchManualAddress(){
        const query = manualAddressInput.value.trim();

        if(!query){
            alert('Masukkan alamat terlebih dahulu.');
            return;
        }

        locationResult.innerHTML = 'Mencari lokasi dari alamat yang dimasukkan...';

        const url =
            'https://nominatim.openstreetmap.org/search' +
            '?format=json' +
            '&limit=1' +
            '&countrycodes=id' +
            '&addressdetails=1' +
            '&q=' + encodeURIComponent(query);

        fetch(url, {
            headers:{
                'Accept':'application/json'
            }
        })
        .then(response => response.json())
        .then(results => {
            if(!results || results.length === 0){
                locationResult.innerHTML = 'Lokasi tidak ditemukan. Coba tulis alamat lebih lengkap.';
                alert('Lokasi tidak ditemukan. Coba masukkan alamat yang lebih lengkap.');
                return;
            }

            const place = results[0];

            const lat = parseFloat(place.lat);
            const lng = parseFloat(place.lon);
            const address = place.display_name || query;

            map.setView([lat, lng], 17);
            customerMarker.setLatLng([lat, lng]);
            customerMarker.bindPopup('Lokasi pelanggan').openPopup();

            setCustomerLocation(lat, lng, address, false);
        })
        .catch(() => {
            locationResult.innerHTML = 'Gagal mencari lokasi. Periksa koneksi internet.';
            alert('Gagal mencari lokasi. Periksa koneksi internet.');
        });
    }

    function useCurrentLocation(){
        if(!navigator.geolocation){
            alert('Browser tidak mendukung fitur lokasi.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position){
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.setView([lat, lng], 17);
                customerMarker.setLatLng([lat, lng]);
                customerMarker.bindPopup('Lokasi pelanggan').openPopup();

                setCustomerLocation(lat, lng, null, true);
            },
            function(){
                alert('Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.');
            }
        );
    }

    function setCustomerLocation(lat, lng, address = null, useReverse = true){
        latitudeInput.value = lat.toFixed(7);
        longitudeInput.value = lng.toFixed(7);

        const distanceKm = calculateDistanceKm(muaLat, muaLng, lat, lng);
        const transport = calculateTransportCost(distanceKm);

        if(address){
            fullAddressInput.value = address;
            manualAddressInput.value = address;
        }else{
            fullAddressInput.value =
                'Koordinat pelanggan: ' + lat.toFixed(7) + ', ' + lng.toFixed(7);
        }

        locationResult.innerHTML =
            '<strong>Alamat:</strong> ' + (fullAddressInput.value || '-') + '<br>' +
            '<strong>Latitude:</strong> ' + lat.toFixed(7) + '<br>' +
            '<strong>Longitude:</strong> ' + lng.toFixed(7) + '<br>' +
            '<strong>Jarak dari lokasi MUA:</strong> ' + distanceKm + ' km';

        transportInfo.innerHTML =
            '<strong>Harga Transport:</strong> ' + formatRupiah(transport.cost) + '<br>' +
            '<strong>Jarak:</strong> ' + distanceKm + ' km<br>' +
            '<strong>Keterangan:</strong> ' + transport.note;

        if(useReverse){
            fetch("{{ route('location.reverse') }}", {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body:JSON.stringify({
                    latitude:lat,
                    longitude:lng
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    fullAddressInput.value = data.full_address;
                    manualAddressInput.value = data.full_address;

                    locationResult.innerHTML =
                        '<strong>Alamat:</strong> ' + data.full_address + '<br>' +
                        '<strong>Latitude:</strong> ' + lat.toFixed(7) + '<br>' +
                        '<strong>Longitude:</strong> ' + lng.toFixed(7) + '<br>' +
                        '<strong>Jarak dari lokasi MUA:</strong> ' + distanceKm + ' km';
                }
            })
            .catch(() => {
                console.log('Alamat otomatis gagal, koordinat tetap tersimpan.');
            });
        }
    }

    function calculateDistanceKm(lat1, lon1, lat2, lon2){
        const earthRadius = 6371;

        const dLat = degToRad(lat2 - lat1);
        const dLon = degToRad(lon2 - lon1);

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(degToRad(lat1)) * Math.cos(degToRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return Math.round((earthRadius * c) * 100) / 100;
    }

    function degToRad(value){
        return value * Math.PI / 180;
    }

    function calculateTransportCost(distanceKm){
        let cost = 0;
        let note = '';

        if(distanceKm <= 5){
            cost = 15000;
            note = 'Jarak 0–5 km';
        }else if(distanceKm <= 10){
            cost = 25000;
            note = 'Jarak 6–10 km';
        }else if(distanceKm <= 15){
            cost = 40000;
            note = 'Jarak 11–15 km';
        }else if(distanceKm <= 20){
            cost = 60000;
            note = 'Jarak 16–20 km';
        }else if(distanceKm <= 30){
            cost = 90000;
            note = 'Jarak 21–30 km';
        }else if(distanceKm <= 40){
            cost = 120000;
            note = 'Jarak 31–40 km';
        }else{
            const ratePerKm = 5000;
            const extraFee = 50000;

            cost = Math.ceil(distanceKm) * ratePerKm + extraFee;
            note = 'Di atas 40 km, Rp5.000 per km + biaya tambahan';
        }

        return {
            cost:cost,
            note:note
        };
    }

    function formatRupiah(number){
        return new Intl.NumberFormat('id-ID', {
            style:'currency',
            currency:'IDR',
            minimumFractionDigits:0
        }).format(number);
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

    manualAddressInput.addEventListener('keydown', function(e){
        if(e.key === 'Enter'){
            e.preventDefault();
            searchManualAddress();
        }
    });

    bookingForm.addEventListener('submit', function(e){
        const validSchedule = validateSchedule();

        if(!validSchedule){
            e.preventDefault();
            return;
        }

        if(!latitudeInput.value || !longitudeInput.value || !fullAddressInput.value){
            e.preventDefault();
            alert('Silakan pilih lokasi pelanggan terlebih dahulu melalui input alamat atau peta.');
        }
    });

    window.onload = function(){
        toggleBuktiPembayaran();
        validateSchedule();
        initMap();
    };
</script>

@endsection
