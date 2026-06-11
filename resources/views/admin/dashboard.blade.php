@php
    $period = $period ?? request('period', 'today');
    $labelPeriod = $labelPeriod ?? 'Hari Ini';

    $totalOrders = $totalOrders ?? 0;
    $unitsSold = $unitsSold ?? 0;
    $totalRevenue = $totalRevenue ?? 0;

    $approvedCount = $approvedCount ?? 0;
    $pendingCount = $pendingCount ?? 0;
    $rejectedCount = $rejectedCount ?? 0;
    $bookingHealth = $bookingHealth ?? 0;

    $ordersChange = $ordersChange ?? 0;
    $revenueChange = $revenueChange ?? 0;

    $topServices = collect($topServices ?? []);
    $categoryContribution = collect($categoryContribution ?? []);
    $recentBookings = collect($recentBookings ?? []);
    $quadrantData = collect($quadrantData ?? []);

    $salesTrend = $salesTrend ?? [
        'labels' => [],
        'values' => [],
    ];

    $heatmap = $heatmap ?? [
        'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'hours' => collect(range(8, 22))->map(fn ($hour) => str_pad($hour, 2, '0', STR_PAD_LEFT)),
        'data' => collect([]),
    ];

    $bestService = $bestService ?? '-';
    $topRevenue = $topRevenue ?? 0;

    /*
    |--------------------------------------------------------------------------
    | Data Chart dibuat di PHP dulu
    |--------------------------------------------------------------------------
    | Tujuannya agar tidak error ParseError di Blade.
    */

    $categoryLabels = $categoryContribution->pluck('service')->values();
    $categoryValues = $categoryContribution->pluck('total')->values();

    $quadrantChartData = $quadrantData->map(function ($item) {
        $orders = (int) data_get($item, 'orders', 0);
        $revenue = (int) data_get($item, 'revenue', 0);
        $service = data_get($item, 'service', 'Tidak Diketahui');

        return [
            'x' => $orders,
            'y' => $revenue,
            'r' => max(8, min(28, $orders * 3)),
            'service' => $service,
        ];
    })->values();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Lashedia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Inter, Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(20, 210, 190, .35), transparent 30%),
                radial-gradient(circle at bottom left, rgba(247, 200, 216, .45), transparent 30%),
                linear-gradient(135deg, #eefaf8, #f8fbff);
            color: #102a43;
            padding: 18px;
        }

        .dashboard-wrapper {
            max-width: 1500px;
            margin: auto;
        }

        .hero-card {
            min-height: 220px;
            border-radius: 34px;
            padding: 36px 42px;
            background:
                linear-gradient(120deg, rgba(255,255,255,.78), rgba(230,255,252,.75)),
                radial-gradient(circle at 80% 20%, rgba(20, 210, 190, .45), transparent 35%);
            box-shadow: 0 24px 60px rgba(27, 94, 94, .14);
            display: flex;
            justify-content: space-between;
            gap: 25px;
            margin-bottom: 22px;
            border: 1px solid rgba(255,255,255,.9);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.78);
            color: #008b83;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .7px;
            box-shadow: 0 12px 30px rgba(0,0,0,.08);
            margin-bottom: 28px;
        }

        .hero-badge span {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #009f98;
        }

        .hero-title {
            font-size: clamp(34px, 5vw, 58px);
            line-height: 1;
            color: #071827;
            margin-bottom: 18px;
            letter-spacing: -2px;
        }

        .hero-description {
            max-width: 780px;
            color: #607080;
            font-size: 14px;
            line-height: 1.8;
        }

        .hero-income {
            min-width: 240px;
            height: fit-content;
            background: rgba(255,255,255,.82);
            border-radius: 24px;
            padding: 22px 24px;
            box-shadow: 0 18px 40px rgba(20, 90, 90, .12);
        }

        .hero-income span {
            display: block;
            color: #72808d;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .hero-income strong {
            display: block;
            font-size: 30px;
            color: #00796f;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .card {
            background: rgba(255,255,255,.80);
            backdrop-filter: blur(16px);
            border-radius: 26px;
            padding: 22px;
            border: 1px solid rgba(255,255,255,.88);
            box-shadow: 0 18px 45px rgba(27, 94, 94, .11);
        }

        .kpi-card {
            min-height: 185px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card::after {
            content: "";
            position: absolute;
            right: -22px;
            top: -26px;
            width: 105px;
            height: 105px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(98, 181, 255, .45), rgba(255, 197, 214, .45));
            transform: rotate(20deg);
        }

        .kpi-title {
            font-size: 14px;
            color: #102a43;
            font-weight: 900;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }

        .kpi-number {
            font-size: 32px;
            font-weight: 950;
            color: #061626;
            line-height: 1.1;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }

        .kpi-label {
            font-size: 12px;
            font-weight: 800;
            color: #008b83;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }

        .kpi-change {
            font-size: 12px;
            font-weight: 900;
            color: #d83933;
            margin-top: 12px;
            position: relative;
            z-index: 2;
        }

        .mini-bars {
            display: flex;
            gap: 6px;
            align-items: end;
            height: 42px;
            position: relative;
            z-index: 2;
        }

        .mini-bars span {
            width: 6px;
            border-radius: 999px;
            background: linear-gradient(#008b83, #7de5db);
        }

        .span-2 { grid-column: span 2; }
        .span-4 { grid-column: span 4; }
        .span-6 { grid-column: span 6; }
        .span-8 { grid-column: span 8; }
        .span-12 { grid-column: span 12; }

        .section-title {
            font-size: 15px;
            color: #102a43;
            margin-bottom: 8px;
            font-weight: 950;
        }

        .section-desc {
            font-size: 12px;
            color: #718096;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .filter-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .filter-btn {
            border: none;
            text-decoration: none;
            color: #304455;
            padding: 12px 22px;
            border-radius: 18px;
            background: rgba(255,255,255,.9);
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 10px 24px rgba(0,0,0,.06);
            transition: .25s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            color: white;
            background: linear-gradient(135deg, #008b83, #1bc9bc);
            box-shadow: 0 14px 32px rgba(0, 139, 131, .28);
        }

        .bar-list {
            display: grid;
            gap: 13px;
        }

        .bar-item {
            display: grid;
            grid-template-columns: 150px 1fr 55px;
            gap: 12px;
            align-items: center;
            font-size: 12px;
            font-weight: 800;
        }

        .bar-track {
            height: 18px;
            background: #edf3f6;
            border-radius: 999px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #6fc6ff, #008b83);
            border-radius: 999px;
        }

        .chart-box {
            height: 320px;
        }

        .small-chart {
            height: 260px;
        }

        .contribution-list {
            display: grid;
            gap: 13px;
            margin-top: 20px;
        }

        .contribution-item {
            display: grid;
            grid-template-columns: 1fr 70px;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 16px;
            background: rgba(255,255,255,.78);
            box-shadow: 0 10px 24px rgba(0,0,0,.05);
            font-size: 13px;
            font-weight: 900;
        }

        .contribution-item small {
            display: block;
            color: #718096;
            margin-top: 4px;
        }

        .risk-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .risk-box {
            background: rgba(255,255,255,.82);
            border-radius: 18px;
            padding: 16px;
            font-weight: 950;
        }

        .risk-box span {
            display: block;
            color: #72808d;
            font-size: 11px;
            margin-bottom: 7px;
        }

        .risk-track {
            height: 18px;
            background: linear-gradient(90deg, #008b83 0 60%, #ffd54a 60% 78%, #ff8a00 78% 90%, #e53935 90%);
            border-radius: 999px;
            margin: 18px 0;
        }

        .status-row {
            margin-top: 18px;
        }

        .status-name {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 7px;
        }

        .status-progress {
            height: 10px;
            background: #edf3f6;
            border-radius: 999px;
            overflow: hidden;
        }

        .status-progress span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #008b83, #ffd54a);
        }

        .heatmap-wrap {
            overflow-x: auto;
        }

        .heatmap-table {
            border-collapse: separate;
            border-spacing: 8px;
            width: 100%;
            min-width: 720px;
        }

        .heatmap-table th {
            font-size: 11px;
            color: #637381;
            text-align: center;
        }

        .heatmap-table td {
            height: 34px;
            border-radius: 9px;
            text-align: center;
            font-size: 10px;
            font-weight: 900;
        }

        .timeline-list {
            display: grid;
            gap: 14px;
        }

        .timeline-item {
            background: rgba(255,255,255,.86);
            padding: 16px;
            border-radius: 18px;
            border-left: 5px solid #008b83;
            box-shadow: 0 10px 24px rgba(0,0,0,.05);
        }

        .timeline-item h5 {
            font-size: 13px;
            color: #0c2537;
            margin-bottom: 7px;
        }

        .timeline-price {
            color: #008b83;
            font-size: 18px;
            font-weight: 950;
            margin-bottom: 6px;
        }

        .timeline-desc {
            color: #718096;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pill {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            background: #e6fff7;
            color: #008b83;
        }

        .export-btn {
            display: inline-flex;
            margin-top: 18px;
            padding: 12px 18px;
            background: #102a43;
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 900;
        }

        .empty-text {
            color: #718096;
            font-size: 13px;
            font-weight: 700;
            padding: 14px 0;
        }

        @media (max-width: 1100px) {
            .span-2,
            .span-4,
            .span-6,
            .span-8,
            .span-12 {
                grid-column: span 12;
            }

            .hero-card {
                flex-direction: column;
            }

            .hero-income {
                width: 100%;
            }
        }

        @media (max-width: 650px) {
            body {
                padding: 10px;
            }

            .hero-card {
                padding: 26px 22px;
                border-radius: 24px;
            }

            .card {
                padding: 18px;
                border-radius: 22px;
            }

            .risk-summary {
                grid-template-columns: 1fr;
            }

            .bar-item {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="dashboard-wrapper">

    {{-- HERO --}}
    <section class="hero-card">
        <div>
            <div class="hero-badge">
                <span></span>
                DASHBOARD RESERVASI LASHEDIA
            </div>

            <h1 class="hero-title">Lashedia Dashboard</h1>

            <p class="hero-description">
                Pantau performa reservasi, transaksi, layanan terlaris, status booking,
                pendapatan, jadwal padat, dan kondisi operasional MUA berdasarkan periode data yang dipilih.
            </p>
        </div>

        <div class="hero-income">
            <span>Revenue {{ $labelPeriod }}</span>
            <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
        </div>
    </section>

    {{-- KPI DAN FILTER --}}
    <section class="grid">

        <div class="card kpi-card span-2">
            <h4 class="kpi-title">Total Orders</h4>
            <div class="kpi-number">{{ $totalOrders }}</div>
            <div class="kpi-label">{{ $labelPeriod }}</div>

            <div class="mini-bars">
                <span style="height:16px"></span>
                <span style="height:30px"></span>
                <span style="height:22px"></span>
                <span style="height:38px"></span>
                <span style="height:18px"></span>
                <span style="height:28px"></span>
            </div>

            <div class="kpi-change">
                {{ $ordersChange >= 0 ? 'Naik' : 'Turun' }}
                {{ abs($ordersChange) }}% dari periode sebelumnya
            </div>
        </div>

        <div class="card kpi-card span-2">
            <h4 class="kpi-title">Units Sold</h4>
            <div class="kpi-number">{{ $unitsSold }}</div>
            <div class="kpi-label">{{ $labelPeriod }}</div>

            <div class="mini-bars">
                <span style="height:18px"></span>
                <span style="height:26px"></span>
                <span style="height:40px"></span>
                <span style="height:34px"></span>
                <span style="height:20px"></span>
                <span style="height:25px"></span>
            </div>

            <div class="kpi-change">
                Total layanan terjual
            </div>
        </div>

        <div class="card kpi-card span-2">
            <h4 class="kpi-title">Total Revenue</h4>
            <div class="kpi-number">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <div class="kpi-label">{{ $labelPeriod }}</div>

            <div class="mini-bars">
                <span style="height:10px"></span>
                <span style="height:18px"></span>
                <span style="height:30px"></span>
                <span style="height:42px"></span>
                <span style="height:34px"></span>
                <span style="height:22px"></span>
            </div>

            <div class="kpi-change">
                {{ $revenueChange >= 0 ? 'Naik' : 'Turun' }}
                {{ abs($revenueChange) }}% dari periode sebelumnya
            </div>
        </div>

        <div class="card kpi-card span-2">
            <h4 class="kpi-title">Booking Health</h4>
            <div class="kpi-number">{{ $bookingHealth }}%</div>
            <div class="kpi-label">
                {{ $approvedCount }} approved · {{ $pendingCount }} pending
            </div>

            <div class="risk-track"></div>

            <div class="kpi-change">
                {{ $rejectedCount }} booking ditolak
            </div>
        </div>

        <div class="card span-4">
            <h4 class="section-title">Dashboard Metric</h4>
            <p class="section-desc">
                Pilih periode untuk mengubah data visualisasi dashboard.
            </p>

            <div class="filter-row">
                <a href="{{ route('admin.dashboard', ['period' => 'today']) }}"
                   class="filter-btn {{ $period === 'today' ? 'active' : '' }}">
                    Hari Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'week']) }}"
                   class="filter-btn {{ $period === 'week' ? 'active' : '' }}">
                    Minggu Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'month']) }}"
                   class="filter-btn {{ $period === 'month' ? 'active' : '' }}">
                    Bulan Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'year']) }}"
                   class="filter-btn {{ $period === 'year' ? 'active' : '' }}">
                    Tahun Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'all']) }}"
                   class="filter-btn {{ $period === 'all' ? 'active' : '' }}">
                    Semua Data
                </a>
            </div>

            @if(\Illuminate\Support\Facades\Route::has('admin.powerbi.bookings.csv'))
                <a href="{{ route('admin.powerbi.bookings.csv') }}" class="export-btn">
                    Export CSV Power BI
                </a>
            @endif
        </div>

    </section>

    {{-- TOP SERVICE DAN SALES TREND --}}
    <section class="grid">

        <div class="card span-4">
            <h4 class="section-title">Top Service Performance</h4>
            <p class="section-desc">
                Top layanan berdasarkan jumlah reservasi.
            </p>

            <div class="bar-list">
                @forelse($topServices->take(10) as $service)
                    @php
                        $max = max($topServices->max('total') ?? 1, 1);
                        $width = ((int) data_get($service, 'total', 0) / $max) * 100;
                    @endphp

                    <div class="bar-item">
                        <span>{{ data_get($service, 'service', 'Tidak Diketahui') }}</span>

                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ $width }}%"></div>
                        </div>

                        <span>{{ data_get($service, 'total', 0) }} item</span>
                    </div>
                @empty
                    <p class="empty-text">Belum ada data layanan.</p>
                @endforelse
            </div>
        </div>

        <div class="card span-8">
            <h4 class="section-title">Sales Trend Overview</h4>
            <p class="section-desc">
                Tren pendapatan berdasarkan periode yang dipilih.
            </p>

            <div class="chart-box">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

    </section>

    {{-- CATEGORY DAN RISK --}}
    <section class="grid">

        <div class="card span-6">
            <h4 class="section-title">Category Contribution</h4>
            <p class="section-desc">
                Kontribusi layanan berdasarkan jumlah booking.
            </p>

            <div class="small-chart">
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="contribution-list">
                @forelse($categoryContribution as $item)
                    <div class="contribution-item">
                        <div>
                            {{ data_get($item, 'service', 'Tidak Diketahui') }}
                            <small>{{ data_get($item, 'total', 0) }} item</small>
                        </div>

                        <strong>{{ data_get($item, 'percentage', 0) }}%</strong>
                    </div>
                @empty
                    <p class="empty-text">Belum ada kontribusi kategori.</p>
                @endforelse
            </div>
        </div>

        <div class="card span-6">
            <h4 class="section-title">Booking Risk</h4>
            <p class="section-desc">
                Distribusi status booking untuk melihat risiko operasional.
            </p>

            <div class="risk-summary">
                <div class="risk-box">
                    <span>Approved</span>
                    {{ $approvedCount }}
                </div>

                <div class="risk-box">
                    <span>Pending</span>
                    {{ $pendingCount }}
                </div>

                <div class="risk-box">
                    <span>Rejected</span>
                    {{ $rejectedCount }}
                </div>
            </div>

            <div class="risk-track"></div>

            <div class="status-row">
                <div class="status-name">
                    <span>Approved</span>
                    <span>{{ $approvedCount }}</span>
                </div>

                <div class="status-progress">
                    <span style="width: {{ $totalOrders > 0 ? ($approvedCount / $totalOrders) * 100 : 0 }}%"></span>
                </div>
            </div>

            <div class="status-row">
                <div class="status-name">
                    <span>Pending</span>
                    <span>{{ $pendingCount }}</span>
                </div>

                <div class="status-progress">
                    <span style="width: {{ $totalOrders > 0 ? ($pendingCount / $totalOrders) * 100 : 0 }}%"></span>
                </div>
            </div>

            <div class="status-row">
                <div class="status-name">
                    <span>Rejected</span>
                    <span>{{ $rejectedCount }}</span>
                </div>

                <div class="status-progress">
                    <span style="width: {{ $totalOrders > 0 ? ($rejectedCount / $totalOrders) * 100 : 0 }}%"></span>
                </div>
            </div>
        </div>

    </section>

    {{-- HEATMAP DAN TIMELINE --}}
    <section class="grid">

        <div class="card span-8">
            <h4 class="section-title">Sales Heatmap</h4>
            <p class="section-desc">
                Intensitas booking berdasarkan hari dan jam.
            </p>

            <div class="heatmap-wrap">
                <table class="heatmap-table">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach($heatmap['hours'] as $hour)
                                <th>{{ $hour }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($heatmap['days'] as $day)
                            <tr>
                                <th>{{ $day }}</th>

                                @foreach($heatmap['hours'] as $hour)
                                    @php
                                        $cell = collect($heatmap['data'])
                                            ->where('day', $day)
                                            ->where('hour', $hour)
                                            ->first();

                                        $count = (int) data_get($cell, 'count', 0);
                                        $opacity = min(1, 0.12 + ($count * 0.18));
                                    @endphp

                                    <td style="background: rgba(0, 139, 131, {{ $opacity }}); color: {{ $count > 2 ? '#fff' : '#006d67' }}">
                                        {{ $count > 0 ? $count : '' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card span-4">
            <h4 class="section-title">Recent Sales Timeline</h4>
            <p class="section-desc">
                Timeline booking terbaru.
            </p>

            <div class="timeline-list">
                @forelse($recentBookings as $booking)
                    <div class="timeline-item">
                        <h5>{{ $booking->name ?? '-' }}</h5>

                        <div class="timeline-price">
                            Rp {{ number_format($booking->dashboard_total ?? 0, 0, ',', '.') }}
                        </div>

                        <div class="timeline-desc">
                            {{ $booking->service ?? '-' }} · {{ $booking->date ?? '-' }} {{ $booking->time ?? '' }}
                        </div>

                        <span class="status-pill">
                            {{ $booking->status ?? '-' }}
                        </span>
                    </div>
                @empty
                    <p class="empty-text">Belum ada booking terbaru.</p>
                @endforelse
            </div>
        </div>

    </section>

    {{-- QUADRANT --}}
    <section class="grid">

        <div class="card span-12">
            <h4 class="section-title">Product Performance Quadrant</h4>
            <p class="section-desc">
                Analisis layanan berdasarkan jumlah booking dan revenue.
            </p>

            <div class="risk-summary">
                <div class="risk-box">
                    <span>Best Service</span>
                    {{ $bestService }}
                </div>

                <div class="risk-box">
                    <span>Top Revenue</span>
                    Rp {{ number_format($topRevenue, 0, ',', '.') }}
                </div>

                <div class="risk-box">
                    <span>Services Analyzed</span>
                    {{ $quadrantData->count() }}
                </div>
            </div>

            <div class="chart-box">
                <canvas id="quadrantChart"></canvas>
            </div>
        </div>

    </section>

</div>

<script>
    function rupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Chart Sales Trend
    |--------------------------------------------------------------------------
    */

    const salesTrendCanvas = document.getElementById('salesTrendChart');

    if (salesTrendCanvas) {
        new Chart(salesTrendCanvas, {
            type: 'line',
            data: {
                labels: @json($salesTrend['labels']),
                datasets: [{
                    label: 'Revenue',
                    data: @json($salesTrend['values']),
                    tension: 0.45,
                    fill: true,
                    borderWidth: 4,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return rupiah(context.raw);
                            }
                        }
                    }
                },

                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return rupiah(value);
                            }
                        }
                    }
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Chart Category Contribution
    |--------------------------------------------------------------------------
    */

    const categoryCanvas = document.getElementById('categoryChart');

    if (categoryCanvas) {
        new Chart(categoryCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($categoryLabels),
                datasets: [{
                    data: @json($categoryValues),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',

                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Chart Quadrant Analysis
    |--------------------------------------------------------------------------
    */

    const quadrantCanvas = document.getElementById('quadrantChart');

    if (quadrantCanvas) {
        new Chart(quadrantCanvas, {
            type: 'bubble',
            data: {
                datasets: [{
                    label: 'Service Performance',
                    data: @json($quadrantChartData)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].raw.service;
                            },

                            label: function(context) {
                                return [
                                    'Orders: ' + context.raw.x,
                                    'Revenue: ' + rupiah(context.raw.y)
                                ];
                            }
                        }
                    }
                },

                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Units Sold'
                        }
                    },

                    y: {
                        title: {
                            display: true,
                            text: 'Revenue'
                        },

                        ticks: {
                            callback: function(value) {
                                return rupiah(value);
                            }
                        }
                    }
                }
            }
        });
    }
</script>

</body>
</html>
