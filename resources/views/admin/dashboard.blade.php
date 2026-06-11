@extends('layouts.admin')

@section('content')

<div class="dashboard-page">

    {{-- HERO --}}
    <section class="dashboard-hero">
        <div>
            <div class="dashboard-badge">
                <span></span>
                DASHBOARD RESERVASI LASHEDIA
            </div>

            <h1>Lashedia Dashboard</h1>

            <p>
                Pantau performa pesanan, transaksi, layanan terlaris, status booking,
                pendapatan, jadwal padat, serta kondisi operasional MUA berdasarkan
                pencatatan pesanan yang masuk ke website.
            </p>
        </div>

        <div class="dashboard-income">
            <small>Revenue {{ $labelPeriod }}</small>
            <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
        </div>
    </section>

    {{-- KPI --}}
    <section class="dashboard-grid">

        <div class="dash-card kpi-card span-2">
            <h4>Total Orders</h4>
            <div class="kpi-number">{{ $totalOrders }}</div>
            <p>{{ $labelPeriod }}</p>

            <div class="mini-bars">
                <span style="height:16px"></span>
                <span style="height:30px"></span>
                <span style="height:22px"></span>
                <span style="height:38px"></span>
                <span style="height:18px"></span>
                <span style="height:28px"></span>
            </div>

            <div class="kpi-note">
                {{ $ordersChange >= 0 ? 'Naik' : 'Turun' }}
                {{ abs($ordersChange) }}% dari periode sebelumnya
            </div>
        </div>

        <div class="dash-card kpi-card span-2">
            <h4>Units Sold</h4>
            <div class="kpi-number">{{ $unitsSold }}</div>
            <p>Total layanan terjual</p>

            <div class="mini-bars">
                <span style="height:18px"></span>
                <span style="height:26px"></span>
                <span style="height:40px"></span>
                <span style="height:34px"></span>
                <span style="height:20px"></span>
                <span style="height:25px"></span>
            </div>

            <div class="kpi-note">Berdasarkan data booking</div>
        </div>

        <div class="dash-card kpi-card span-2">
            <h4>Total Revenue</h4>
            <div class="kpi-number money">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <p>{{ $labelPeriod }}</p>

            <div class="mini-bars">
                <span style="height:10px"></span>
                <span style="height:18px"></span>
                <span style="height:30px"></span>
                <span style="height:42px"></span>
                <span style="height:34px"></span>
                <span style="height:22px"></span>
            </div>

            <div class="kpi-note">
                {{ $revenueChange >= 0 ? 'Naik' : 'Turun' }}
                {{ abs($revenueChange) }}% dari periode sebelumnya
            </div>
        </div>

        <div class="dash-card kpi-card span-2">
            <h4>Booking Health</h4>
            <div class="kpi-number">{{ $bookingHealth }}%</div>
            <p>{{ $approvedCount }} approved · {{ $pendingCount }} pending</p>

            <div class="risk-line"></div>

            <div class="kpi-note">
                {{ $rejectedCount }} booking ditolak
            </div>
        </div>

        <div class="dash-card span-4">
            <h4 class="section-title">Dashboard Metric</h4>
            <p class="section-desc">
                Pilih periode agar data dashboard berubah otomatis.
            </p>

            <div class="filter-row">
                <a href="{{ route('admin.dashboard', ['period' => 'today']) }}"
                   class="{{ $period === 'today' ? 'active' : '' }}">
                    Hari Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'week']) }}"
                   class="{{ $period === 'week' ? 'active' : '' }}">
                    Minggu Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'month']) }}"
                   class="{{ $period === 'month' ? 'active' : '' }}">
                    Bulan Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'year']) }}"
                   class="{{ $period === 'year' ? 'active' : '' }}">
                    Tahun Ini
                </a>

                <a href="{{ route('admin.dashboard', ['period' => 'all']) }}"
                   class="{{ $period === 'all' ? 'active' : '' }}">
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
    <section class="dashboard-grid">

        <div class="dash-card span-4">
            <h4 class="section-title">Top Service Performance</h4>
            <p class="section-desc">Layanan yang paling banyak dipesan pelanggan.</p>

            <div class="bar-list">
                @forelse($topServices->take(10) as $service)
                    @php
                        $max = max($topServices->max('total') ?? 1, 1);
                        $width = ((int) $service['total'] / $max) * 100;
                    @endphp

                    <div class="bar-item">
                        <span>{{ $service['service'] }}</span>

                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ $width }}%"></div>
                        </div>

                        <b>{{ $service['total'] }}</b>
                    </div>
                @empty
                    <p class="empty-text">Belum ada data layanan.</p>
                @endforelse
            </div>
        </div>

        <div class="dash-card span-8">
            <h4 class="section-title">Sales Trend Overview</h4>
            <p class="section-desc">Tren pendapatan berdasarkan periode yang dipilih.</p>

            <div class="chart-box">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

    </section>

    {{-- CATEGORY DAN RISK --}}
    <section class="dashboard-grid">

        <div class="dash-card span-6">
            <h4 class="section-title">Category Contribution</h4>
            <p class="section-desc">Kontribusi layanan berdasarkan jumlah booking.</p>

            <div class="small-chart">
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="contribution-list">
                @forelse($categoryContribution as $item)
                    <div class="contribution-item">
                        <div>
                            {{ $item['service'] }}
                            <small>{{ $item['total'] }} item</small>
                        </div>

                        <strong>{{ $item['percentage'] }}%</strong>
                    </div>
                @empty
                    <p class="empty-text">Belum ada kontribusi kategori.</p>
                @endforelse
            </div>
        </div>

        <div class="dash-card span-6">
            <h4 class="section-title">Booking Risk</h4>
            <p class="section-desc">Distribusi status pesanan dari pencatatan booking.</p>

            <div class="risk-summary">
                <div>
                    <span>Approved</span>
                    <strong>{{ $approvedCount }}</strong>
                </div>

                <div>
                    <span>Pending</span>
                    <strong>{{ $pendingCount }}</strong>
                </div>

                <div>
                    <span>Rejected</span>
                    <strong>{{ $rejectedCount }}</strong>
                </div>
            </div>

            <div class="risk-line"></div>

            <div class="status-row">
                <div>
                    <span>Approved</span>
                    <b>{{ $approvedCount }}</b>
                </div>

                <div class="status-track">
                    <span style="width: {{ $totalOrders > 0 ? ($approvedCount / $totalOrders) * 100 : 0 }}%"></span>
                </div>
            </div>

            <div class="status-row">
                <div>
                    <span>Pending</span>
                    <b>{{ $pendingCount }}</b>
                </div>

                <div class="status-track">
                    <span style="width: {{ $totalOrders > 0 ? ($pendingCount / $totalOrders) * 100 : 0 }}%"></span>
                </div>
            </div>

            <div class="status-row">
                <div>
                    <span>Rejected</span>
                    <b>{{ $rejectedCount }}</b>
                </div>

                <div class="status-track">
                    <span style="width: {{ $totalOrders > 0 ? ($rejectedCount / $totalOrders) * 100 : 0 }}%"></span>
                </div>
            </div>
        </div>

    </section>

    {{-- HEATMAP DAN TIMELINE --}}
    <section class="dashboard-grid">

        <div class="dash-card span-8">
            <h4 class="section-title">Sales Heatmap</h4>
            <p class="section-desc">Intensitas booking berdasarkan hari dan jam.</p>

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
                                    $opacity = min(1, 0.10 + ($count * 0.18));
                                @endphp

                                <td style="background: rgba(217, 107, 159, {{ $opacity }}); color: {{ $count > 2 ? '#fff' : '#9b2f67' }}">
                                    {{ $count > 0 ? $count : '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dash-card span-4">
            <h4 class="section-title">Recent Sales Timeline</h4>
            <p class="section-desc">Pesanan terbaru yang masuk ke website.</p>

            <div class="timeline-list">
                @forelse($recentBookings as $booking)
                    <div class="timeline-item">
                        <h5>{{ $booking->name ?? '-' }}</h5>

                        <strong>
                            Rp {{ number_format($booking->dashboard_total ?? 0, 0, ',', '.') }}
                        </strong>

                        <p>
                            {{ $booking->service ?? '-' }} ·
                            {{ $booking->date ?? '-' }}
                            {{ $booking->time ?? '' }}
                        </p>

                        <span>{{ $booking->status ?? '-' }}</span>
                    </div>
                @empty
                    <p class="empty-text">Belum ada booking terbaru.</p>
                @endforelse
            </div>
        </div>

    </section>

    {{-- QUADRANT --}}
    <section class="dashboard-grid">

        <div class="dash-card span-12">
            <h4 class="section-title">Product Performance Quadrant</h4>
            <p class="section-desc">
                Analisis layanan berdasarkan jumlah booking dan revenue.
            </p>

            <div class="risk-summary">
                <div>
                    <span>Best Service</span>
                    <strong>{{ $bestService }}</strong>
                </div>

                <div>
                    <span>Top Revenue</span>
                    <strong>Rp {{ number_format($topRevenue, 0, ',', '.') }}</strong>
                </div>

                <div>
                    <span>Services Analyzed</span>
                    <strong>{{ $quadrantData->count() }}</strong>
                </div>
            </div>

            <div class="chart-box">
                <canvas id="quadrantChart"></canvas>
            </div>
        </div>

    </section>

</div>

@endsection

@push('styles')
<style>
    .dashboard-page {
        width: 100%;
    }

    .dashboard-hero {
        min-height: 210px;
        border-radius: 34px;
        padding: 36px 42px;
        margin-bottom: 22px;
        display: flex;
        justify-content: space-between;
        gap: 24px;
        background:
            linear-gradient(120deg, rgba(255,255,255,.86), rgba(255,241,247,.86)),
            radial-gradient(circle at 80% 20%, rgba(247,200,216,.8), transparent 35%);
        box-shadow: var(--shadow-soft);
        border: 1px solid rgba(255,255,255,.9);
    }

    .dashboard-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 999px;
        background: rgba(255,255,255,.82);
        color: var(--pink-dark);
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .7px;
        margin-bottom: 26px;
        box-shadow: 0 12px 24px rgba(217, 107, 159, .12);
    }

    .dashboard-badge span {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--pink-dark);
    }

    .dashboard-hero h1 {
        font-size: clamp(34px, 5vw, 58px);
        line-height: 1;
        color: var(--text-dark);
        margin-bottom: 16px;
        letter-spacing: -2px;
    }

    .dashboard-hero p {
        max-width: 780px;
        color: var(--text-muted);
        font-size: 14px;
        line-height: 1.8;
    }

    .dashboard-income {
        min-width: 240px;
        height: fit-content;
        padding: 22px 24px;
        border-radius: 24px;
        background: rgba(255,255,255,.86);
        box-shadow: 0 18px 40px rgba(217,107,159,.14);
    }

    .dashboard-income small {
        display: block;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 850;
        margin-bottom: 8px;
    }

    .dashboard-income strong {
        display: block;
        color: var(--pink-dark);
        font-size: 30px;
        font-weight: 950;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 18px;
        margin-bottom: 22px;
    }

    .dash-card {
        background: var(--white-glass);
        backdrop-filter: blur(16px);
        border-radius: 26px;
        padding: 22px;
        border: 1px solid rgba(255,255,255,.9);
        box-shadow: var(--shadow-soft);
    }

    .span-2 { grid-column: span 2; }
    .span-4 { grid-column: span 4; }
    .span-6 { grid-column: span 6; }
    .span-8 { grid-column: span 8; }
    .span-12 { grid-column: span 12; }

    .kpi-card {
        position: relative;
        overflow: hidden;
        min-height: 180px;
    }

    .kpi-card::after {
        content: "";
        position: absolute;
        top: -28px;
        right: -22px;
        width: 105px;
        height: 105px;
        border-radius: 30px;
        background: linear-gradient(135deg, var(--pink-main), var(--purple-soft));
        transform: rotate(20deg);
        opacity: .75;
    }

    .kpi-card h4,
    .kpi-number,
    .kpi-card p,
    .kpi-note,
    .mini-bars {
        position: relative;
        z-index: 2;
    }

    .kpi-card h4,
    .section-title {
        font-size: 15px;
        color: var(--text-dark);
        font-weight: 950;
        margin-bottom: 10px;
    }

    .kpi-number {
        font-size: 32px;
        font-weight: 950;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .kpi-number.money {
        font-size: 24px;
    }

    .kpi-card p,
    .section-desc {
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 750;
        margin-bottom: 16px;
    }

    .kpi-note {
        margin-top: 12px;
        color: var(--pink-dark);
        font-size: 12px;
        font-weight: 900;
    }

    .mini-bars {
        display: flex;
        gap: 6px;
        align-items: end;
        height: 42px;
    }

    .mini-bars span {
        width: 6px;
        border-radius: 999px;
        background: linear-gradient(var(--pink-dark), var(--pink-main));
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
    }

    .filter-row a,
    .export-btn {
        text-decoration: none;
        border: 1px solid rgba(217,107,159,.22);
        background: rgba(255,255,255,.84);
        color: var(--text-dark);
        padding: 11px 17px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        transition: .25s;
    }

    .filter-row a:hover,
    .filter-row a.active,
    .export-btn:hover {
        background: linear-gradient(135deg, var(--pink-main), var(--pink-dark));
        color: white;
        border-color: transparent;
        box-shadow: 0 12px 24px rgba(217,107,159,.2);
    }

    .export-btn {
        display: inline-flex;
        margin-top: 18px;
    }

    .bar-list,
    .contribution-list,
    .timeline-list {
        display: grid;
        gap: 13px;
    }

    .bar-item {
        display: grid;
        grid-template-columns: 145px 1fr 38px;
        gap: 12px;
        align-items: center;
        font-size: 12px;
        font-weight: 850;
    }

    .bar-track {
        height: 18px;
        background: #f5e8f0;
        border-radius: 999px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--pink-main), var(--pink-dark));
        border-radius: 999px;
    }

    .chart-box {
        height: 320px;
    }

    .small-chart {
        height: 260px;
    }

    .contribution-item,
    .timeline-item,
    .risk-summary div {
        background: rgba(255,255,255,.76);
        border-radius: 18px;
        padding: 15px;
        box-shadow: 0 10px 24px rgba(217,107,159,.08);
    }

    .contribution-item {
        display: grid;
        grid-template-columns: 1fr 70px;
        gap: 12px;
        font-size: 13px;
        font-weight: 900;
    }

    .contribution-item small,
    .risk-summary span,
    .timeline-item p {
        display: block;
        color: var(--text-muted);
        font-size: 12px;
        margin-top: 4px;
    }

    .risk-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .risk-summary strong {
        display: block;
        margin-top: 6px;
        font-size: 18px;
        color: var(--pink-dark);
    }

    .risk-line {
        height: 18px;
        border-radius: 999px;
        margin: 18px 0;
        background: linear-gradient(90deg, #6ee7b7 0 60%, #fde68a 60% 82%, #fb7185 82%);
    }

    .status-row {
        margin-top: 18px;
    }

    .status-row > div:first-child {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 7px;
    }

    .status-track {
        height: 10px;
        background: #f5e8f0;
        border-radius: 999px;
        overflow: hidden;
    }

    .status-track span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--pink-main), var(--pink-dark));
    }

    .heatmap-wrap {
        overflow-x: auto;
    }

    .heatmap-table {
        width: 100%;
        min-width: 720px;
        border-collapse: separate;
        border-spacing: 8px;
    }

    .heatmap-table th {
        font-size: 11px;
        color: var(--text-muted);
        text-align: center;
    }

    .heatmap-table td {
        height: 34px;
        border-radius: 9px;
        text-align: center;
        font-size: 10px;
        font-weight: 900;
    }

    .timeline-item {
        border-left: 5px solid var(--pink-dark);
    }

    .timeline-item h5 {
        font-size: 14px;
        margin-bottom: 6px;
    }

    .timeline-item strong {
        color: var(--pink-dark);
        font-size: 18px;
        font-weight: 950;
    }

    .timeline-item span {
        display: inline-block;
        margin-top: 10px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        background: #fff1f7;
        color: var(--pink-dark);
    }

    .empty-text {
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 750;
        padding: 12px 0;
    }

    @media (max-width: 1100px) {
        .span-2,
        .span-4,
        .span-6,
        .span-8,
        .span-12 {
            grid-column: span 12;
        }

        .dashboard-hero {
            flex-direction: column;
        }

        .dashboard-income {
            width: 100%;
        }
    }

    @media (max-width: 650px) {
        .dashboard-hero {
            padding: 26px 22px;
            border-radius: 24px;
        }

        .dash-card {
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
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function rupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
    }

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
@endpush
