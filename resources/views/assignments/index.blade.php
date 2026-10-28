@extends('layouts.app')

@section('title', 'Daftar Penugasan')
@section('page-title', 'Penugasan')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN PENUGASAN</p>

                <h1>Daftar Penugasan</h1>

                <p class="activity-page-description">
                    Pantau petugas yang ditugaskan pada setiap kegiatan.
                </p>
            </div>

            <div class="activity-heading-actions">
                <span class="admin-demo-badge">
                    <span class="admin-demo-dot"></span>
                    Data Database
                </span>

                <a href="{{ route('admin.assignments.create') }}" class="activity-button-primary">
                    + Tambah Penugasan
                </a>
            </div>
        </section>

        @if (session('success'))
            <div class="activity-success-message" role="status">
                <strong>Berhasil.</strong>
                {{ session('success') }}
            </div>
        @endif

        <section class="activity-summary">
            <div>
                <span class="activity-summary-label">
                    Total Penugasan
                </span>

                <strong>{{ number_format($assignments->total()) }}</strong>
            </div>

            <p>
                Menampilkan {{ $assignments->count() }}
                dari {{ $assignments->total() }} penugasan
            </p>
        </section>

        {{-- Pencarian --}}
        <section class="activity-filter-panel">
            <form action="{{ route('admin.assignments.index') }}" method="GET" class="assignment-search-form">
                <div class="activity-search-field">
                    <label for="assignment-search">
                        Cari kegiatan atau petugas
                    </label>

                    <input id="assignment-search" name="search" type="search" value="{{ $search }}"
                        placeholder="Nama/kode kegiatan atau nama petugas" class="activity-filter-control">
                </div>

                <div class="activity-filter-actions">
                    <button type="submit" class="activity-button-primary">
                        Cari
                    </button>

                    <a href="{{ route('admin.assignments.index') }}" class="activity-button-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Tabel penugasan --}}
        <section class="admin-panel activity-table-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Data Penugasan</h2>

                    <p>
                        Satu baris mewakili satu petugas dalam satu kegiatan.
                    </p>
                </div>

                <span class="admin-panel-period">
                    {{ $assignments->total() }} data
                </span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table assignment-table">
                    <thead>
                        <tr>
                            <th scope="col">Kegiatan</th>
                            <th scope="col">Petugas</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Tanggal Penugasan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($assignments as $assignment)
                                        <tr>
                                            <td>
                                                <span class="admin-activity-name">
                                                    {{ $assignment->activity?->name
                            ?? 'Kegiatan tidak tersedia'
                                                                                                                                                            }}
                                                </span>

                                                <span class="admin-activity-note">
                                                    {{ $assignment->activity?->code ?? '—' }}

                                                    @if ($assignment->activity?->activityType)
                                                        · {{ $assignment->activity->activityType->name }}
                                                    @endif
                                                </span>
                                            </td>

                                            <td>
                                                {{ $assignment->user?->name
                            ?? 'Pengguna tidak tersedia'
                                                                                                                                                        }}
                                            </td>

                                            <td>
                                                @if ($assignment->assignment_role)
                                                    <span class="admin-status admin-status-scheduled">
                                                        {{ $assignment->assignment_role }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400">
                                                        Belum ditentukan
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $assignment->created_at?->format('d/m/Y') ?? '—' }}
                                            </td>

                                            <td>
                                                <div class="activity-row-actions">
                                                    <a href="{{ route('admin.assignments.edit', $assignment) }}" class="activity-row-link">
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>

                                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="admin-empty-message">
                                    @if ($search !== '')
                                        Tidak ada penugasan yang sesuai dengan pencarian.
                                    @else
                                        Belum ada penugasan.
                                        Gunakan tombol Tambah Penugasan untuk memulai.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($assignments->hasPages())
                <div class="activity-pagination">
                    {{ $assignments->links() }}
                </div>
            @endif

        </section>

    </div>
@endsection