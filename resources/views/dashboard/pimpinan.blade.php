@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')
@section('page-title', 'Monitoring Kegiatan')

@section('content')
    <div class="admin-dashboard">

        {{-- Heading --}}
        <section class="admin-dashboard-heading">
            <div>
                <p class="admin-eyebrow">OVERVIEW</p>

                <h1>Monitoring Kegiatan</h1>

                <p class="admin-heading-description">
                    Ringkasan kegiatan layanan pendidikan.
                </p>
            </div>

            <span class="admin-demo-badge">
                <span class="admin-demo-dot"></span>
                Data Database
            </span>
        </section>

        {{-- Keterangan sumber data --}}
        <div class="admin-demo-notice">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 16v-4"></path>
                <path d="M12 8h.01"></path>
            </svg>

            <p>
                <strong>Data database.</strong>
                Ringkasan dihitung berdasarkan status kegiatan.
                Grafik tren menggunakan tanggal pelaksanaan jadwal,
                sedangkan distribusi berdasarkan jenis kegiatan.
            </p>
        </div>

        {{-- KPI --}}
        <section class="admin-kpi-grid" aria-label="Ringkasan kegiatan">

            {{-- Total Kegiatan --}}
            <article class="admin-kpi-card">
                <div class="admin-kpi-top">
                    <span class="admin-kpi-label">
                        Total Kegiatan
                    </span>

                    <span class="admin-kpi-icon admin-icon-navy">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                            <path d="M16 2v4M8 2v4M3 10h18"></path>
                            <path d="M8 14h3M8 17h6"></path>
                        </svg>
                    </span>
                </div>

                <p class="admin-kpi-value">
                    {{ number_format($summary['total']) }}
                </p>

                <p class="admin-kpi-caption">
                    Seluruh status kegiatan
                </p>
            </article>

            {{-- Selesai --}}
            <article class="admin-kpi-card">
                <div class="admin-kpi-top">
                    <span class="admin-kpi-label">Selesai</span>

                    <span class="admin-kpi-icon admin-icon-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m8 12 2.5 2.5L16 9"></path>
                        </svg>
                    </span>
                </div>

                <p class="admin-kpi-value">
                    {{ number_format($summary['completed']) }}
                </p>

                <p class="admin-kpi-caption">
                    Kegiatan berstatus selesai
                </p>
            </article>

            {{-- Berlangsung --}}
            <article class="admin-kpi-card">
                <div class="admin-kpi-top">
                    <span class="admin-kpi-label">Berlangsung</span>

                    <span class="admin-kpi-icon admin-icon-gold">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M10 8.5l5 3.5-5 3.5z"></path>
                        </svg>
                    </span>
                </div>

                <p class="admin-kpi-value">
                    {{ number_format($summary['ongoing']) }}
                </p>

                <p class="admin-kpi-caption">
                    Kegiatan berstatus berlangsung
                </p>
            </article>

            {{-- Terjadwal --}}
            <article class="admin-kpi-card">
                <div class="admin-kpi-top">
                    <span class="admin-kpi-label">Terjadwal</span>

                    <span class="admin-kpi-icon admin-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                    </span>
                </div>

                <p class="admin-kpi-value">
                    {{ number_format($summary['scheduled']) }}
                </p>

                <p class="admin-kpi-caption">
                    Kegiatan berstatus terjadwal
                </p>
            </article>

        </section>

        {{-- Grafik --}}
        <section class="admin-chart-grid">

            {{-- Tren jadwal --}}
            <article class="admin-panel">
                <header class="admin-panel-header">
                    <div>
                        <h2>Tren Jadwal Kegiatan</h2>

                        <p>
                            Jumlah jadwal berdasarkan tanggal pelaksanaan
                        </p>
                    </div>

                    <span class="admin-panel-period">
                        6 bulan terakhir
                    </span>
                </header>

                <div class="admin-chart-body">
                    <svg class="admin-line-chart" viewBox="0 0 600 230" role="img"
                        aria-label="Grafik jumlah jadwal kegiatan selama enam bulan terakhir"
                        preserveAspectRatio="xMidYMid meet">
                        <desc>
                            Jumlah jadwal kegiatan per bulan berdasarkan
                            tanggal pelaksanaan di database.
                        </desc>

                        {{-- Garis bantu --}}
                        <g class="admin-chart-gridlines">
                            @foreach ($chartTicks as $tick)
                                <line x1="48" y1="{{ $tick['y'] }}" x2="565" y2="{{ $tick['y'] }}"></line>
                            @endforeach
                        </g>

                        {{-- Label sumbu Y --}}
                        <g class="admin-chart-axis-labels">
                            @foreach ($chartTicks as $tick)
                                <text x="34" y="{{ $tick['y'] + 4 }}" text-anchor="end">{{ $tick['label'] }}</text>
                            @endforeach
                        </g>

                        {{-- Area grafik --}}
                        <path class="admin-chart-area" d="{{ $areaPath }}"></path>

                        {{-- Garis grafik --}}
                        <path class="admin-chart-line" d="{{ $linePath }}"></path>

                        {{-- Titik data --}}
                        <g class="admin-chart-points">
                            @foreach ($chartPoints as $point)
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4">
                                    <title>{{ $point['label'] }}: {{ $point['total'] }} jadwal</title>
                                </circle>
                            @endforeach
                        </g>

                        {{-- Label sumbu X --}}
                        <g class="admin-chart-axis-labels admin-chart-months">
                            @foreach ($chartPoints as $point)
                                <text x="{{ $point['x'] }}" y="207" text-anchor="middle">{{ $point['label'] }}</text>
                            @endforeach
                        </g>
                    </svg>
                </div>

                <div class="admin-chart-footer">
                    <span class="admin-legend-line"></span>

                    <span>
                        Jumlah jadwal berdasarkan database
                    </span>
                </div>
            </article>

            {{-- Distribusi jenis --}}
            <article class="admin-panel">
                <header class="admin-panel-header">
                    <div>
                        <h2>Distribusi Jenis Kegiatan</h2>

                        <p>
                            Proporsi kegiatan berdasarkan jenisnya
                        </p>
                    </div>

                    <span class="admin-panel-period">Database</span>
                </header>

                <div class="admin-distribution-body">
                    <div class="admin-donut" style="background: {{ $donutGradient }};" role="img"
                        aria-label="Distribusi kegiatan berdasarkan jenis dari database">
                        <div class="admin-donut-center">
                            <strong>
                                {{ number_format($summary['total']) }}
                            </strong>

                            <span>Total kegiatan</span>
                        </div>
                    </div>

                    <div class="admin-legend">
                        @forelse ($typeDistribution as $type)
                            <div class="admin-legend-item">
                                <span class="admin-legend-dot" style="background-color: {{ $type['color'] }};"></span>

                                <span class="admin-legend-name">
                                    {{ $type['name'] }}
                                </span>

                                <strong>
                                    {{ number_format($type['percentage'], 1) }}%
                                </strong>
                            </div>
                        @empty
                            <p class="admin-empty-message">
                                Belum ada data jenis kegiatan.
                            </p>
                        @endforelse
                    </div>
                </div>
            </article>

        </section>

    </div>
@endsection