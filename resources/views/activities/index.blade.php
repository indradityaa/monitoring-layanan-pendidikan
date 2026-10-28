@extends('layouts.app')

@section('title', 'Daftar Kegiatan')
@section('page-title', 'Kegiatan')

@section('content')
    <div class="activity-page">

        {{-- Heading --}}
        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN KEGIATAN</p>

                <h1>Daftar Kegiatan</h1>

                <p class="activity-page-description">
                    Kelola dan pantau kegiatan layanan pendidikan.
                </p>
            </div>

            <div class="activity-heading-actions">
                <span class="admin-demo-badge">
                    <span class="admin-demo-dot"></span>
                    Data Database
                </span>

                <a href="{{ route('admin.activities.create') }}" class="activity-button-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>

                    Tambah Kegiatan
                </a>
            </div>

        </section>

        @if (session('success'))
            <div class="activity-success-message" role="status">
                <strong>Berhasil.</strong>
                {{ session('success') }}
            </div>
        @endif

        {{-- Ringkasan jumlah hasil --}}
        <section class="activity-summary">
            <div>
                <span class="activity-summary-label">
                    Total hasil
                </span>

                <strong>{{ number_format($activities->total()) }}</strong>
            </div>

            <p>
                Menampilkan {{ $activities->count() }} dari
                {{ $activities->total() }} kegiatan
            </p>
        </section>

        {{-- Pencarian dan filter --}}
        <section class="activity-filter-panel">
            <form action="{{ route('admin.activities.index') }}" method="GET" class="activity-filter-form">
                <div class="activity-search-field">
                    <label for="activity-search">Cari kegiatan</label>

                    <input type="search" id="activity-search" name="search" value="{{ $search }}"
                        placeholder="Masukkan nama atau kode kegiatan..." class="activity-filter-control">
                </div>

                <div class="activity-select-field">
                    <label for="activity-status">Status</label>

                    <select id="activity-status" name="status" class="activity-filter-control">
                        <option value="">Semua status</option>

                        @foreach ($statusMeta as $value => $meta)
                            <option value="{{ $value }}" @selected($statusFilter === $value)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="activity-select-field">
                    <label for="activity-type">Jenis kegiatan</label>

                    <select id="activity-type" name="activity_type_id" class="activity-filter-control">
                        <option value="">Semua jenis</option>

                        @foreach ($activityTypes as $type)
                            <option value="{{ $type->id }}" @selected((string) $typeFilter === (string) $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="activity-filter-actions">
                    <button type="submit" class="activity-button-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        Terapkan
                    </button>

                    <a href="{{ route('admin.activities.index') }}" class="activity-button-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Tabel --}}
        <section class="admin-panel activity-table-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Data Kegiatan</h2>

                    <p>
                        Daftar kegiatan yang tersimpan dalam database.
                    </p>
                </div>

                <span class="admin-panel-period">
                    {{ $activities->total() }} data
                </span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table activity-table">
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama Kegiatan</th>
                            <th scope="col">Jenis</th>
                            <th scope="col">Penanggung Jawab</th>
                            <th scope="col">Jadwal</th>
                            <th scope="col">Dibuat</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($activities as $activity)
                                        @php
                                            $status = $statusMeta[$activity->status] ?? [
                                                'label' => ucfirst($activity->status),
                                                'class' => 'admin-status-planned',
                                            ];
                                        @endphp

                                        <tr>
                                            <td>
                                                <span class="activity-code">
                                                    {{ $activity->code }}
                                                </span>
                                            </td>

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

                                            <td>
                                                {{ $activity->activityType?->name ?? '—' }}
                                            </td>

                                            <td>
                                                {{ $activity->personInCharge?->name
                            ?? 'Belum ditentukan'
                                                                                                                                                                                }}
                                            </td>

                                            <td>
                                                {{ number_format($activity->schedules_count) }}
                                                jadwal
                                            </td>

                                            <td>
                                                {{ $activity->created_at?->format('d/m/Y') ?? '—' }}
                                            </td>

                                            <td>
                                                <span class="admin-status {{ $status['class'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="activity-row-actions">
                                                    <a href="{{ route('admin.activities.show', $activity) }}" class="activity-row-link">
                                                        Detail
                                                    </a>

                                                    <a href="{{ route('admin.activities.edit', $activity) }}" class="activity-row-link">
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>

                                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="admin-empty-message">
                                    @if (
                                            $search !== '' ||
                                            $statusFilter !== '' ||
                                            $typeFilter !== ''
                                        )
                                        Tidak ada kegiatan yang sesuai dengan
                                        pencarian atau filter.
                                    @else
                                        Belum ada kegiatan di database.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($activities->hasPages())
                <div class="activity-pagination">
                    {{ $activities->links() }}
                </div>
            @endif
        </section>

    </div>
@endsection