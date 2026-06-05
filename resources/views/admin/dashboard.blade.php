@extends('layouts.admin')

@section('content')

<style>
    .powerbi-wrapper{
        padding:40px 0;
    }

    .powerbi-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:24px;
        margin-bottom:34px;
    }

    .powerbi-title h1{
        margin:0;
        font-size:52px;
        color:#4b4453;
        font-family:'Playfair Display', serif;
        font-weight:900;
    }

    .powerbi-title p{
        margin-top:12px;
        color:#7a7283;
        font-size:15px;
        font-weight:600;
        line-height:1.7;
    }

    .powerbi-date{
        background:#f7c8d8;
        color:#3f3548;
        padding:14px 24px;
        border-radius:999px;
        font-weight:900;
        box-shadow:0 12px 28px rgba(247,200,216,.35);
        white-space:nowrap;
    }

    .stat-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
        gap:22px;
        margin-bottom:32px;
    }

    .stat-card{
        background:#ffffff;
        border:1px solid #ffe0ea;
        border-radius:28px;
        padding:26px;
        box-shadow:0 14px 34px rgba(247,200,216,.14);
        transition:.25s ease;
        position:relative;
        overflow:hidden;
    }

    .stat-card::before{
        content:"";
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:7px;
        background:linear-gradient(135deg,#f7c8d8,#c8b6ff);
    }

    .stat-card:hover{
        transform:translateY(-5px);
        box-shadow:0 20px 44px rgba(247,200,216,.26);
    }

    .stat-card span{
        display:block;
        color:#7a7283;
        font-size:14px;
        font-weight:800;
        margin-bottom:12px;
    }

    .stat-card h2{
        margin:0;
        font-size:42px;
        color:#4b4453;
        font-weight:900;
    }

    .chart-grid{
        display:grid;
        grid-template-columns:1.4fr .9fr;
        gap:26px;
        margin-bottom:34px;
    }

    .chart-card{
        background:#ffffff;
        border:1px solid #ffe0ea;
        border-radius:30px;
        padding:28px;
        box-shadow:0 16px 38px rgba(247,200,216,.16);
    }

    .chart-card h2{
        margin:0 0 8px 0;
        color:#4b4453;
        font-size:32px;
        font-family:'Playfair Display', serif;
        font-weight:900;
    }

    .chart-card p{
        margin:0 0 24px 0;
        color:#7a7283;
        font-size:14px;
        font-weight:600;
    }

    .bar-chart{
        display:flex;
        flex-direction:column;
        gap:18px;
    }

    .bar-row{
        display:grid;
        grid-template-columns:160px 1fr 48px;
        align-items:center;
        gap:14px;
    }

    .bar-label{
        font-size:14px;
        font-weight:800;
        color:#4b4453;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .bar-track{
        width:100%;
        height:34px;
        background:#fff1f6;
        border-radius:999px;
        overflow:hidden;
        border:1px solid #ffe0ea;
    }

    .bar-fill{
        height:100%;
        min-width:34px;
        border-radius:999px;
        background:linear-gradient(135deg,#f7c8d8,#c8b6ff);
        box-shadow:0 8px 18px rgba(247,200,216,.35);
        transition:.35s ease;
    }

    .bar-row:hover .bar-fill{
        filter:brightness(.96);
        transform:scaleX(1.01);
        transform-origin:left;
    }

    .bar-value{
        font-size:14px;
        font-weight:900;
        color:#4b4453;
        text-align:right;
    }

    .status-chart{
        display:flex;
        flex-direction:column;
        gap:18px;
    }

    .status-bar-fill{
        height:100%;
        min-width:34px;
        border-radius:999px;
        background:#f7c8d8;
        box-shadow:0 8px 18px rgba(247,200,216,.35);
        transition:.35s ease;
    }

    .bar-row:hover .status-bar-fill{
        filter:brightness(.96);
        transform:scaleX(1.01);
        transform-origin:left;
    }

    /* GRAFIK STYLIST */
    .stylist-chart-card{
        margin-bottom:34px;
    }

    .stylist-bar-fill{
        height:100%;
        min-width:34px;
        border-radius:999px;
        background:linear-gradient(135deg,#c8b6ff,#f7c8d8);
        box-shadow:0 8px 18px rgba(200,182,255,.35);
        transition:.35s ease;
    }

    .bar-row:hover .stylist-bar-fill{
        filter:brightness(.96);
        transform:scaleX(1.01);
        transform-origin:left;
    }

    .powerbi-report{
        background:#ffffff;
        border:1px solid #ffe0ea;
        border-radius:30px;
        padding:28px;
        box-shadow:0 16px 38px rgba(247,200,216,.16);
        margin-bottom:34px;
    }

    .powerbi-report h2{
        margin:0 0 20px 0;
        color:#4b4453;
        font-size:32px;
        font-family:'Playfair Display', serif;
        font-weight:900;
    }

    .powerbi-frame{
        width:100%;
        height:620px;
        border:0;
        border-radius:24px;
        background:#fff7fb;
    }

    .powerbi-empty{
        background:linear-gradient(135deg,#fff7fb,#faf7ff);
        border:1px dashed #f3a8c4;
        border-radius:24px;
        padding:44px;
        text-align:center;
        color:#7a7283;
        font-weight:800;
        line-height:1.8;
    }

    .booking-preview{
        background:#ffffff;
        border:1px solid #ffe0ea;
        border-radius:30px;
        padding:28px;
        box-shadow:0 16px 38px rgba(247,200,216,.14);
    }

    .booking-preview h2{
        margin:0 0 20px 0;
        color:#4b4453;
        font-size:32px;
        font-family:'Playfair Display', serif;
        font-weight:900;
    }

    .booking-table{
        width:100%;
        border-collapse:collapse;
    }

    .booking-table th,
    .booking-table td{
        padding:15px 12px;
        border-bottom:1px solid #f8dce7;
        text-align:left;
        font-size:14px;
        color:#4b4453;
    }

    .booking-table th{
        background:#fff7fb;
        color:#7a7283;
        font-weight:900;
    }

    .status-badge{
        display:inline-flex;
        padding:8px 14px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
    }

    .status-pending{
        background:#fff4cc;
        color:#8a6500;
    }

    .status-approved{
        background:#dcfce7;
        color:#166534;
    }

    .status-rejected{
        background:#fee2e2;
        color:#991b1b;
    }

    @media(max-width:900px){
        .chart-grid{
            grid-template-columns:1fr;
        }
    }

    @media(max-width:768px){
        .powerbi-header{
            flex-direction:column;
            align-items:flex-start;
        }

        .powerbi-title h1{
            font-size:38px;
        }

        .bar-row{
            grid-template-columns:1fr;
            gap:8px;
        }

        .bar-value{
            text-align:left;
        }

        .powerbi-frame{
            height:420px;
        }

        .booking-table{
            display:block;
            overflow-x:auto;
            white-space:nowrap;
        }
    }
</style>

<div class="powerbi-wrapper">

    <div class="powerbi-header">

        <div class="powerbi-title">
            <h1>Power BI Dashboard</h1>

            <p>
                Dashboard ini menampilkan ringkasan pesan pelanggan, status booking,
                layanan terpopuler, stylist, invoice, kalender, dan proses admin Lashedia.
            </p>
        </div>

        <div class="powerbi-date">
            {{ now()->format('d M Y') }}
        </div>

    </div>

    <div class="stat-grid">

        <div class="stat-card">
            <span>Total Pesan Pelanggan</span>
            <h2>{{ $totalBookings }}</h2>
        </div>

        <div class="stat-card">
            <span>Booking Pending</span>
            <h2>{{ $pendingBookings }}</h2>
        </div>

        <div class="stat-card">
            <span>Booking Approved</span>
            <h2>{{ $approvedBookings }}</h2>
        </div>

        <div class="stat-card">
            <span>Booking Rejected</span>
            <h2>{{ $rejectedBookings }}</h2>
        </div>

    </div>

    <div class="chart-grid">

        <div class="chart-card">

            <h2>Diagram Batang Layanan</h2>

            <p>
                Grafik ini menampilkan jumlah pesan pelanggan berdasarkan jenis layanan.
            </p>

            <div class="bar-chart">

                @forelse($serviceStats as $item)

                    @php
                        $width = $maxServiceTotal > 0
                            ? ($item->total / $maxServiceTotal) * 100
                            : 0;
                    @endphp

                    <div class="bar-row">

                        <div class="bar-label">
                            {{ $item->service ?? 'Tidak diketahui' }}
                        </div>

                        <div class="bar-track">
                            <div
                                class="bar-fill"
                                style="width: {{ $width }}%;"
                            ></div>
                        </div>

                        <div class="bar-value">
                            {{ $item->total }}
                        </div>

                    </div>

                @empty

                    <div class="powerbi-empty">
                        Belum ada data layanan untuk ditampilkan.
                    </div>

                @endforelse

            </div>

        </div>

        <div class="chart-card">

            <h2>Status Booking</h2>

            <p>
                Grafik status proses admin berdasarkan data booking.
            </p>

            <div class="status-chart">

                @foreach($statusStats as $status)

                    @php
                        $width = $maxStatusTotal > 0
                            ? ($status['total'] / $maxStatusTotal) * 100
                            : 0;
                    @endphp

                    <div class="bar-row">

                        <div class="bar-label">
                            {{ $status['label'] }}
                        </div>

                        <div class="bar-track">
                            <div
                                class="status-bar-fill"
                                style="width: {{ $width }}%;"
                            ></div>
                        </div>

                        <div class="bar-value">
                            {{ $status['total'] }}
                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

    {{-- GRAFIK STYLIST --}}
    <div class="chart-card stylist-chart-card">

        <h2>Diagram Batang Stylist</h2>

        <p>
            Grafik ini menampilkan jumlah booking pelanggan berdasarkan penata rias atau stylist yang dipilih.
        </p>

        <div class="bar-chart">

            @forelse($stylistStats as $item)

                @php
                    $width = $maxStylistTotal > 0
                        ? ($item->total / $maxStylistTotal) * 100
                        : 0;
                @endphp

                <div class="bar-row">

                    <div class="bar-label">
                        {{ $item->stylist ?? 'Tidak diketahui' }}
                    </div>

                    <div class="bar-track">
                        <div
                            class="stylist-bar-fill"
                            style="width: {{ $width }}%;"
                        ></div>
                    </div>

                    <div class="bar-value">
                        {{ $item->total }}
                    </div>

                </div>

            @empty

                <div class="powerbi-empty">
                    Belum ada data stylist untuk ditampilkan.
                </div>

            @endforelse

        </div>

    </div>

    <div class="powerbi-report">

        <h2>Power BI Report Embed</h2>

        @if(!empty($powerBiEmbedUrl))

            <iframe
                class="powerbi-frame"
                src="{{ $powerBiEmbedUrl }}"
                allowfullscreen="true"
            ></iframe>

        @else

            <div class="powerbi-empty">
                Link Power BI belum tersedia. Tambahkan POWER_BI_EMBED_URL di file .env.
                Untuk sementara, grafik diagram batang di atas sudah mengambil data langsung dari booking pelanggan.
            </div>

        @endif

    </div>

    <div class="booking-preview">

        <h2>Preview Pesan Pelanggan</h2>

        <table class="booking-table">

            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Layanan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Stylist</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse($latestBookings as $booking)

                    <tr>
                        <td>{{ $booking->name }}</td>
                        <td>{{ $booking->service }}</td>
                        <td>{{ $booking->date }}</td>
                        <td>{{ $booking->time }}</td>
                        <td>{{ $booking->stylist ?? '-' }}</td>
                        <td>
                            @if($booking->status === 'Approved')
                                <span class="status-badge status-approved">
                                    Approved
                                </span>
                            @elseif($booking->status === 'Rejected')
                                <span class="status-badge status-rejected">
                                    Rejected
                                </span>
                            @else
                                <span class="status-badge status-pending">
                                    Pending
                                </span>
                            @endif
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            Belum ada data pesan pelanggan.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
