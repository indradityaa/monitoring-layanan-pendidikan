@extends('layouts.app')

@section('title', 'Master Data Lokasi')
@section('page-title', 'Lokasi')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MASTER DATA</p>

                <h1>Lokasi</h1>

                <p class="activity-page-description">
                    Kelola nama, alamat, kecamatan, dan kota lokasi kegiatan.
                </p>
            </div>

            <a href="{{ route('admin.master.locations.create') }}" class="activity-button-primary">
                + Tambah Lokasi
            </a>
        </section>

        @if (session('success'))
            <div class="activity-success-message" role="status">
                <strong>Berhasil.</strong>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Permintaan tidak dapat diproses.</strong>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        {{-- Ringkasan --}}
        <section class="activity-summary">
            <div>
                <span class="activity-summary-label">Total hasil</span>

                <strong>{{ number_format($locations->total()) }}</strong>
            </div>

            <p>
                Menampilkan {{ $locations->count() }}
                dari {{ $locations->total() }} lokasi
            </p>
        </section>

        {{-- Pencarian dan filter --}}
        <section class="activity-filter-panel">
            <form action="{{ route('admin.master.locations.index') }}" method="GET" class="activity-type-filter-form">
                <div class="activity-search-field">
                    <label for="location-search">Cari lokasi</label>

                    <input id="location-search" name="search" type="search" value="{{ $search }}" maxlength="150"
                        placeholder="Nama, alamat, kecamatan, atau kota..." class="activity-filter-control">
                </div>

                <div class="activity-select-field">
                    <label for="location-active">Status</label>

                    <select id="location-active" name="active" class="activity-filter-control">
                        <option value="">Semua status</option>

                        <option value="1" @selected($activeFilter === '1')>
                            Aktif
                        </option>

                        <option value="0" @selected($activeFilter === '0')>
                            Nonaktif
                        </option>
                    </select>
                </div>

                <div class="activity-filter-actions">
                    <button type="submit" class="activity-button-primary">
                        Cari
                    </button>

                    <a href="{{ route('admin.master.locations.index') }}" class="activity-button-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Tabel lokasi --}}
        <section class="admin-panel activity-table-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Data Lokasi</h2>

                    <p>
                        Lokasi yang dapat digunakan pada jadwal kegiatan.
                    </p>
                </div>

                <span class="admin-panel-period">
                    {{ $locations->total() }} data
                </span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table location-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Lokasi</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Kecamatan</th>
                            <th scope="col">Kota/Kabupaten</th>
                            <th scope="col">Jadwal Terkait</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($locations as $location)
                                            <tr>
                                                <td>
                                                    <span class="admin-activity-name">
                                                        {{ $location->name }}
                                                    </span>
                                                </td>

                                                <td>
                                                    {{ $location->address
                                ? \Illuminate\Support\Str::limit(
                                    $location->address,
                                    70
                                )
                                : '—'
                                                                                }}
                                                </td>

                                                <td>{{ $location->district ?: '—' }}</td>

                                                <td>{{ $location->city ?: '—' }}</td>

                                                <td>
                                                    {{ number_format($location->schedules_count) }}
                                                    jadwal
                                                </td>

                                                <td>
                                                    @if ($location->is_active)
                                                        <span class="admin-status admin-status-completed">
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="admin-status admin-status-cancelled">
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="activity-row-actions">
                                                        <a href="{{ route(
                                'admin.master.locations.edit',
                                $location
                            ) }}" class="activity-row-link">
                                                            Edit
                                                        </a>

                                                        <form action="{{ route(
                                'admin.master.locations.toggle-active',
                                $location
                            ) }}" method="POST"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin {{ $location->is_active ? 'menonaktifkan' : 'mengaktifkan' }} lokasi ini?');">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit" class="activity-row-link activity-row-button">
                                                                {{ $location->is_active
                                ? 'Nonaktifkan'
                                : 'Aktifkan'
                                                                                            }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="admin-empty-message">
                                    @if ($search !== '' || $activeFilter !== '')
                                        Tidak ada lokasi yang sesuai dengan
                                        pencarian atau filter.
                                    @else
                                        Belum ada lokasi yang tercatat dalam sistem.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($locations->hasPages())
                <div class="activity-pagination">
                    {{ $locations->links() }}
                </div>
            @endif

        </section>

    </div>
@endsection