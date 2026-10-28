@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
    <div class="admin-dashboard">

        {{-- Heading dashboard --}}
        <section class="admin-dashboard-heading">
            <div>
                <p class="admin-eyebrow">OVERVIEW</p>
                <h1>Dashboard Admin</h1>
                <p class="admin-heading-description">
                    Pantau ringkasan kegiatan layanan pendidikan.
                </p>
            </div>

            <span class="admin-demo-badge">
                <span class="admin-demo-dot"></span>
                Mode Demo
            </span>
        </section>

        {{-- Penanda bahwa angka belum berasal dari data operasional --}}
        <div class="admin-demo-notice">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 16v-4"></path>
                <path d="M12 8h.01"></path>
            </svg>

            <p>
            <p>
                <strong>Data database.</strong>
                Ringkasan dan daftar kegiatan diambil dari database aplikasi.
                Grafik tren menghitung jadwal berdasarkan tanggal pelaksanaan,
                sedangkan distribusi menunjukkan proporsi kegiatan berdasarkan
                jenisnya.
            </p>

            </p>
        </div>

        {{-- Ringkasan KPI --}}
        <section class="admin-kpi-grid" aria-label="Ringkasan kegiatan">

            <article class="admin-kpi-card">
                <div class="admin-kpi-top">
                    <span class="admin-kpi-label">Total Kegiatan</span>

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
                <p class="admin-kpi-caption">Seluruh status kegiatan</p>
            </article>

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
                <p class="admin-kpi-caption">Menunggu waktu pelaksanaan</p>
            </article>

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
                <p class="admin-kpi-caption">Sedang dilaksanakan</p>
            </article>

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
                <p class="admin-kpi-caption">Telah selesai dilaksanakan</p>
            </article>

        </section>

        {{-- Grafik --}}
        <section class="admin-chart-grid">

            {{-- Grafik tren --}}
            <article class="admin-panel">
                <header class="admin-panel-header">
                    <div>
                        <h2>Tren Jadwal Kegiatan</h2>
                        <p>Jumlah jadwal berdasarkan tanggal pelaksanaan</p>
                    </div>

                    <span class="admin-panel-period">6 bulan terakhir</span>
                </header>

                <div class="admin-chart-body">
                    <svg class="admin-line-chart" viewBox="0 0 600 230" role="img"
                        aria-label="Grafik jumlah jadwal kegiatan selama enam bulan terakhir"
                        preserveAspectRatio="xMidYMid meet">
                        <desc>
                            Jumlah catatan kegiatan yang dibuat setiap bulan
                            selama enam bulan terakhir.
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
                                    <title>{{ $point['label'] }}: {{ $point['total'] }} kegiatan</title>
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
                    <span>Jumlah jadwal berdasarkan tanggal pelaksanaan</span>
                </div>
            </article>

            {{-- Grafik distribusi --}}
            <article class="admin-panel">
                <header class="admin-panel-header">
                    <div>
                        <h2>Distribusi Jenis Kegiatan</h2>
                        <p>Proporsi kegiatan berdasarkan jenisnya</p>
                    </div>

                    <span class="admin-panel-period">Database</span>
                </header>

                <div class="admin-distribution-body">
                    <div class="admin-donut" style="background: {{ $donutGradient }};" role="img"
                        aria-label="Distribusi kegiatan berdasarkan jenis dari database">
                        <div class="admin-donut-center">
                            <strong>{{ number_format($summary['total']) }}</strong>
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

        {{-- Tabel kegiatan terbaru --}}
        <section class="admin-panel admin-recent-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>Kegiatan Terbaru</h2>
                    <p>Lima kegiatan terbaru beserta jadwal dan lokasinya</p>
                </div>

                <span class="admin-panel-period">Database</span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Kegiatan</th>
                            <th scope="col">Kode</th>
                            <th scope="col">Jenis</th>
                            <th scope="col">Jadwal Pelaksanaan</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Status Kegiatan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($latestActivities as $activity)
                                        @php
                                            $status = $statusMeta[$activity->status] ?? [
                                                'label' => ucfirst($activity->status),
                                                'class' => 'admin-status-planned',
                                            ];

                                            $schedule = $activity->schedules->first();
                                        @endphp

                                        <tr>
                                            {{-- Nama kegiatan --}}
                                            <td>
                                                <span class="admin-activity-name">
                                                    {{ $activity->name }}
                                                </span>

                                                <span class="admin-activity-note">
                                                    {{ $activity->description
                            ? \Illuminate\Support\Str::limit(
                                $activity->description,
                                70
                            )
                            : 'Tidak ada deskripsi'
                                                                                                                                                                }}
                                                </span>
                                            </td>

                                            {{-- Kode kegiatan --}}
                                            <td>{{ $activity->code }}</td>

                                            {{-- Jenis kegiatan --}}
                                            <td>
                                                {{ $activity->activityType?->name ?? '—' }}
                                            </td>

                                            {{-- Tanggal dan jam pelaksanaan --}}
                                            <td>
                                                @if ($schedule)
                                                                    <span class="admin-activity-name">
                                                                        {{ $schedule->schedule_date?->format('d/m/Y') ?? '—' }}
                                                                    </span>

                                                                    <span class="admin-activity-note">
                                                                        {{ $schedule->start_time
                                                    ? substr($schedule->start_time, 0, 5)
                                                    : '—'
                                                                                                                                                                                                                                                                                                                    }}
                                                                        –
                                                                        {{ $schedule->end_time
                                                    ? substr($schedule->end_time, 0, 5)
                                                    : '—'
                                                                                                                                                                                                                                                                                                                    }}
                                                                    </span>
                                                @else
                                                    <span>Belum dijadwalkan</span>
                                                @endif
                                            </td>

                                            {{-- Lokasi kegiatan --}}
                                            <td>
                                                @if ($schedule?->location)
                                                                    <span class="admin-activity-name">
                                                                        {{ $schedule->location->name }}
                                                                    </span>

                                                                    <span class="admin-activity-note">
                                                                        {{ $schedule->location->city
                                                    ?? $schedule->location->district
                                                    ?? ' '
                                                                                                                                                                                                                                                                                                                    }}
                                                                    </span>
                                                @else
                                                    <span>—</span>
                                                @endif
                                            </td>

                                            {{-- Status kegiatan --}}
                                            <td>
                                                <span class="admin-status {{ $status['class'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="admin-empty-message">
                                    Belum ada kegiatan di database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </section>

    </div>
@endsection