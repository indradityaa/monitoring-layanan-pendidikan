@extends('layouts.app')

@section('title', 'Detail Kegiatan')
@section('page-title', 'Detail Kegiatan')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN KEGIATAN</p>
                <h1>Detail Kegiatan</h1>
                <p class="activity-page-description">
                    Informasi lengkap kegiatan layanan pendidikan.
                </p>
            </div>

            <div class="activity-heading-actions">
                <a href="{{ route('admin.activities.index') }}" class="activity-button-secondary">
                    Kembali
                </a>

                <a href="{{ route('admin.activities.edit', $activity) }}" class="activity-button-primary">
                    Edit Kegiatan
                </a>
            </div>
        </section>

        @if (session('success'))
            <div class="activity-success-message" role="status">
                <strong>Berhasil.</strong>
                {{ session('success') }}
            </div>
        @endif

        @php
            $status = $statusMeta[$activity->status] ?? [
                'label' => ucfirst($activity->status),
                'class' => 'admin-status-planned',
            ];
        @endphp

        <section class="admin-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>{{ $activity->name }}</h2>
                    <p>Kode kegiatan: {{ $activity->code }}</p>
                </div>

                <span class="admin-status {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </header>

            <div class="activity-detail-grid">

                <div class="activity-detail-item">
                    <span>Jenis Kegiatan</span>
                    <strong>{{ $activity->activityType?->name ?? '—' }}</strong>
                </div>

                <div class="activity-detail-item">
                    <span>Penanggung Jawab</span>
                    <strong>{{ $activity->personInCharge?->name ?? 'Belum ditentukan' }}</strong>
                </div>

                <div class="activity-detail-item">
                    <span>Target Peserta</span>
                    <strong>
                        {{ $activity->target_participants !== null
        ? number_format($activity->target_participants) . ' orang'
        : 'Belum ditentukan'
                                    }}
                    </strong>
                </div>

                <div class="activity-detail-item">
                    <span>Dibuat oleh</span>
                    <strong>{{ $activity->creator?->name ?? '—' }}</strong>
                </div>

                <div class="activity-detail-item">
                    <span>Tanggal Dibuat</span>
                    <strong>{{ $activity->created_at?->format('d/m/Y H:i') ?? '—' }}</strong>
                </div>

                <div class="activity-detail-item">
                    <span>Terakhir Diperbarui</span>
                    <strong>{{ $activity->updated_at?->format('d/m/Y H:i') ?? '—' }}</strong>
                </div>

                <div class="activity-detail-item activity-detail-full">
                    <span>Deskripsi Kegiatan</span>
                    <p>
                        {{ $activity->description ?: 'Tidak ada deskripsi.' }}
                    </p>
                </div>

            </div>
        </section>

        <section class="admin-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>Jadwal Pelaksanaan</h2>
                    <p>Jadwal yang terkait dengan kegiatan ini.</p>
                </div>

                <span class="admin-panel-period">
                    {{ $activity->schedules->count() }} jadwal
                </span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Lokasi</th>
                            <th>Status Jadwal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($activity->schedules as $schedule)
                                        <tr>
                                            <td>
                                                {{ $schedule->schedule_date?->format('d/m/Y') ?? '—' }}
                                            </td>

                                            <td>
                                                {{ $schedule->start_time
                            ? substr($schedule->start_time, 0, 5)
                            : '—'
                                                                                                                        }}
                                                –
                                                {{ $schedule->end_time
                            ? substr($schedule->end_time, 0, 5)
                            : '—'
                                                                                                                        }}
                                            </td>

                                            <td>
                                                {{ $schedule->location?->name ?? '—' }}
                                            </td>

                                            @php
                                                $scheduleStatusLabels = [
                                                    'scheduled' => 'Terjadwal',
                                                    'rescheduled' => 'Dijadwalkan Ulang',
                                                    'cancelled' => 'Dibatalkan',
                                                    'completed' => 'Selesai',
                                                ];
                                            @endphp

                                            <td>
                                                {{ $scheduleStatusLabels[$schedule->status]
                            ?? ucfirst(str_replace('_', ' ', $schedule->status))
                                                    }}
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="admin-empty-message">
                                    Kegiatan ini belum memiliki jadwal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>Laporan Kegiatan</h2>
                    <p>Informasi keberadaan record laporan terkait.</p>
                </div>
            </header>

            <div class="activity-detail-report">
                @if ($activity->report)
                    <span class="admin-status admin-status-scheduled">
                        {{ ucfirst(str_replace('_', ' ', $activity->report->status)) }}
                    </span>

                    <span class="text-sm text-slate-600">
                        Record laporan tersedia.
                    </span>
                @else
                    <span class="text-sm text-slate-500">
                        Belum ada laporan untuk kegiatan ini.
                    </span>
                @endif
            </div>
        </section>

    </div>
@endsection