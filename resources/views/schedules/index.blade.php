@extends('layouts.app')

@section('title', 'Daftar Jadwal')
@section('page-title', 'Jadwal')

@section('content')
    <div class="activity-page">

        {{-- Heading --}}
        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN JADWAL</p>

                <h1>Daftar Jadwal</h1>

                <p class="activity-page-description">
                    Kelola tanggal, waktu, dan lokasi kegiatan layanan pendidikan.
                </p>
            </div>

            <a href="{{ route('admin.schedules.create') }}" class="activity-button-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                Tambah Jadwal
            </a>
        </section>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="activity-success-message" role="status">
                <strong>Berhasil.</strong>
                {{ session('success') }}
            </div>
        @endif

        {{-- Ringkasan --}}
        <section class="activity-summary">
            <div>
                <span class="activity-summary-label">Total hasil</span>
                <strong>{{ number_format($schedules->total()) }}</strong>
            </div>

            <p>
                Menampilkan {{ $schedules->count() }}
                dari {{ $schedules->total() }} jadwal
            </p>
        </section>

        {{-- Filter --}}
        <section class="activity-filter-panel">
            <form action="{{ route('admin.schedules.index') }}" method="GET" class="schedule-filter-form">
                <div class="activity-search-field">
                    <label for="schedule-search">Cari jadwal</label>

                    <input id="schedule-search" type="search" name="search" value="{{ $search }}"
                        placeholder="Nama/kode kegiatan atau catatan" class="activity-filter-control">
                </div>

                <div class="activity-select-field">
                    <label for="schedule-status">Status jadwal</label>

                    <select id="schedule-status" name="status" class="activity-filter-control">
                        <option value="">Semua status</option>

                        @foreach ($statusMeta as $value => $meta)
                            <option value="{{ $value }}" @selected($statusFilter === $value)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="activity-select-field">
                    <label for="date-from">Dari tanggal</label>

                    <input id="date-from" type="date" name="date_from" value="{{ $dateFrom }}"
                        class="activity-filter-control">
                </div>

                <div class="activity-select-field">
                    <label for="date-to">Sampai tanggal</label>

                    <input id="date-to" type="date" name="date_to" value="{{ $dateTo }}" min="{{ $dateFrom ?: '' }}"
                        class="activity-filter-control">
                </div>

                <div class="activity-filter-actions">
                    <button type="submit" class="activity-button-primary">
                        Terapkan
                    </button>

                    <a href="{{ route('admin.schedules.index') }}" class="activity-button-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Tabel jadwal --}}
        <section class="admin-panel activity-table-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Data Jadwal</h2>
                    <p>Daftar jadwal tersimpan dalam database.</p>
                </div>

                <span class="admin-panel-period">
                    {{ $schedules->total() }} data
                </span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table schedule-table">
                    <thead>
                        <tr>
                            <th scope="col">Kegiatan</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Waktu</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Status Jadwal</th>
                            <th scope="col">Catatan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($schedules as $schedule)
                                        @php
                                            $status = $statusMeta[$schedule->status] ?? [
                                                'label' => ucfirst($schedule->status),
                                                'class' => 'admin-status-planned',
                                            ];
                                        @endphp

                                        <tr>
                                            <td>
                                                <span class="admin-activity-name">
                                                    {{ $schedule->activity?->name ?? 'Kegiatan tidak tersedia' }}
                                                </span>

                                                <span class="admin-activity-note">
                                                    {{ $schedule->activity?->code ?? '—' }}

                                                    @if ($schedule->activity?->activityType)
                                                        · {{ $schedule->activity->activityType->name }}
                                                    @endif
                                                </span>
                                            </td>

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
                                                <span class="admin-activity-name">
                                                    {{ $schedule->location?->name ?? '—' }}
                                                </span>

                                                <span class="admin-activity-note">
                                                    {{ $schedule->location?->city
                            ?? $schedule->location?->district
                            ?? ''
                                                                                                                                                                }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="admin-status {{ $status['class'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ $schedule->notes
                            ? \Illuminate\Support\Str::limit($schedule->notes, 55)
                            : '—'
                                                                                                                                                            }}
                                            </td>
                                            <td>
                                                <div class="activity-row-actions">
                                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="activity-row-link">
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>

                                        </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="admin-empty-message">
                                    @if (
                                            $search !== '' ||
                                            $statusFilter !== '' ||
                                            $dateFrom !== '' ||
                                            $dateTo !== ''
                                        )
                                        Tidak ada jadwal yang sesuai dengan
                                        pencarian atau filter.
                                    @else
                                        Belum ada jadwal di database.
                                        Gunakan tombol Tambah Jadwal untuk membuatnya.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($schedules->hasPages())
                <div class="activity-pagination">
                    {{ $schedules->links() }}
                </div>
            @endif

        </section>

    </div>
@endsection