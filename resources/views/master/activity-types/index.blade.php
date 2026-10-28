@extends('layouts.app')

@section('title', 'Jenis Kegiatan')
@section('page-title', 'Master Data')

@section('content')
    <div class="activity-page">

        {{-- Heading --}}
        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MASTER DATA</p>

                <h1>Jenis Kegiatan</h1>

                <p class="activity-page-description">
                    Kelola kategori kegiatan yang digunakan dalam sistem.
                </p>
            </div>

            <a href="{{ route('admin.master.activity-types.create') }}" class="activity-button-primary">
                + Tambah Jenis
            </a>
        </section>

        {{-- Notifikasi --}}
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
                <strong>{{ number_format($activityTypes->total()) }}</strong>
            </div>

            <p>
                Menampilkan {{ $activityTypes->count() }}
                dari {{ $activityTypes->total() }} jenis kegiatan
            </p>
        </section>

        {{-- Filter --}}
        <section class="activity-filter-panel">
            <form action="{{ route('admin.master.activity-types.index') }}" method="GET" class="activity-type-filter-form">
                <div class="activity-search-field">
                    <label for="type-search">
                        Cari jenis kegiatan
                    </label>

                    <input id="type-search" type="search" name="search" value="{{ $search }}" maxlength="100"
                        placeholder="Nama atau deskripsi..." class="activity-filter-control">
                </div>

                <div class="activity-select-field">
                    <label for="type-active">Status</label>

                    <select id="type-active" name="active" class="activity-filter-control">
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

                    <a href="{{ route('admin.master.activity-types.index') }}" class="activity-button-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Tabel --}}
        <section class="admin-panel activity-table-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Data Jenis Kegiatan</h2>

                    <p>
                        Jenis kegiatan yang tersedia sebagai kategori
                        dalam sistem.
                    </p>
                </div>

                <span class="admin-panel-period">
                    {{ $activityTypes->total() }} data
                </span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table activity-type-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Jenis</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Kegiatan Terkait</th>
                            <th scope="col">Status</th>
                            <th scope="col">Diperbarui</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($activityTypes as $type)
                                            <tr>
                                                <td>
                                                    <span class="admin-activity-name">
                                                        {{ $type->name }}
                                                    </span>
                                                </td>

                                                <td>
                                                    {{ $type->description
                                ? \Illuminate\Support\Str::limit(
                                    $type->description,
                                    90
                                )
                                : '—'
                                                                                                        }}
                                                </td>

                                                <td>
                                                    {{ number_format($type->activities_count) }}
                                                    kegiatan
                                                </td>

                                                <td>
                                                    @if ($type->is_active)
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
                                                    {{ $type->updated_at?->format('d/m/Y') ?? '—' }}
                                                </td>

                                                <td>
                                                    <div class="activity-row-actions">
                                                        <a href="{{ route(
                                'admin.master.activity-types.edit',
                                $type
                            ) }}" class="activity-row-link">
                                                            Edit
                                                        </a>

                                                        <form action="{{ route(
                                'admin.master.activity-types.toggle-active',
                                $type
                            ) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin {{ $type->is_active ? 'menonaktifkan' : 'mengaktifkan' }} jenis kegiatan ini?');">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit" class="activity-row-link activity-row-button">
                                                                {{ $type->is_active
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
                                <td colspan="6" class="admin-empty-message">
                                    @if ($search !== '' || $activeFilter !== '')
                                        Tidak ada jenis kegiatan yang sesuai
                                        dengan pencarian atau filter.
                                    @else
                                        Belum ada jenis kegiatan di database.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activityTypes->hasPages())
                <div class="activity-pagination">
                    {{ $activityTypes->links() }}
                </div>
            @endif

        </section>

    </div>
@endsection