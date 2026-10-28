@extends('layouts.app')

@section('title', $isAdmin ? 'Daftar Laporan' : 'Laporan Kegiatan')
@section('page-title', $isAdmin ? 'Laporan' : 'Laporan Kegiatan')

@section('content')
    <div class="activity-page">

        {{-- Heading --}}
        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN LAPORAN</p>

                <h1>
                    {{ $isAdmin ? 'Daftar Laporan' : 'Laporan Kegiatan Saya' }}
                </h1>

                <p class="activity-page-description">
                    @if ($isAdmin)
                        Pantau laporan kegiatan yang masuk dan perlu divalidasi.
                    @else
                        Pantau laporan dari kegiatan yang ditugaskan kepada Anda.
                    @endif
                </p>
            </div>

            <div class="activity-heading-actions">
                <span class="admin-demo-badge">
                    <span class="admin-demo-dot"></span>
                    Data Database
                </span>

                @unless ($isAdmin)
                    <a href="{{ route('petugas.reports.create') }}" class="activity-button-primary">
                        + Buat Laporan
                    </a>
                @endunless
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
                <span class="activity-summary-label">Total hasil</span>
                <strong>{{ number_format($reports->total()) }}</strong>
            </div>

            <p>
                Menampilkan {{ $reports->count() }}
                dari {{ $reports->total() }} laporan
            </p>
        </section>

        {{-- Pencarian dan filter --}}
        <section class="activity-filter-panel">
            <form action="{{ route($indexRoute) }}" method="GET" class="report-filter-form">
                <div class="activity-search-field">
                    <label for="report-search">Cari laporan</label>

                    <input id="report-search" name="search" type="search" value="{{ $search }}"
                        placeholder="Nama/kode kegiatan atau isi laporan" class="activity-filter-control">
                </div>

                <div class="activity-select-field">
                    <label for="report-status">Status laporan</label>

                    <select id="report-status" name="status" class="activity-filter-control">
                        <option value="">Semua status</option>

                        @foreach ($statusMeta as $value => $meta)
                            <option value="{{ $value }}" @selected($statusFilter === $value)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="activity-filter-actions">
                    <button type="submit" class="activity-button-primary">
                        Cari
                    </button>

                    <a href="{{ route($indexRoute) }}" class="activity-button-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Tabel laporan --}}
        <section class="admin-panel activity-table-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Data Laporan</h2>

                    <p>
                        {{ $isAdmin
        ? 'Laporan seluruh kegiatan dalam sistem.'
        : 'Laporan kegiatan yang ditugaskan kepada Anda.'
                                                            }}
                    </p>
                </div>

                <span class="admin-panel-period">
                    {{ $reports->total() }} data
                </span>
            </header>

            <div class="admin-table-wrapper">
                <table class="admin-table report-table">
                    <thead>
                        <tr>
                            <th scope="col">Kegiatan</th>
                            <th scope="col">Jenis</th>
                            <th scope="col">Pengirim</th>
                            <th scope="col">Tanggal Pengiriman</th>
                            <th scope="col">Status Laporan</th>
                            <th scope="col">Validator</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($reports as $report)
                                        @php
                                            $status = $statusMeta[$report->status] ?? [
                                                'label' => ucfirst($report->status),
                                                'class' => 'admin-status-planned',
                                            ];
                                        @endphp

                                        <tr>
                                            {{-- Informasi kegiatan --}}
                                            <td>
                                                <span class="admin-activity-name">
                                                    {{ $report->activity?->name
                            ?? 'Kegiatan tidak tersedia'
                                                                                                                                                                                                                                                                                            }}
                                                </span>

                                                <span class="admin-activity-note">
                                                    {{ $report->activity?->code ?? '—' }}
                                                </span>

                                                @if ($report->revision_note)
                                                                        <span class="report-revision-note">
                                                                            Catatan revisi:
                                                                            {{ \Illuminate\Support\Str::limit(
                                                        $report->revision_note,
                                                        80
                                                    ) }}
                                                                        </span>
                                                @endif
                                            </td>

                                            {{-- Jenis kegiatan --}}
                                            <td>
                                                {{ $report->activity?->activityType?->name ?? '—' }}
                                            </td>

                                            {{-- Pengirim --}}
                                            <td>
                                                {{ $report->submitter?->name ?? 'Belum dikirim' }}
                                            </td>

                                            {{-- Waktu pengiriman --}}
                                            <td>
                                                {{ $report->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                                            </td>

                                            {{-- Status --}}
                                            <td>
                                                <span class="admin-status {{ $status['class'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </td>

                                            {{-- Validator --}}
                                            <td>
                                                {{ $report->validator?->name ?? '—' }}
                                            </td>
                                            <td>
                                                <div class="activity-row-actions">

                                                    {{-- Detail dapat dibuka Admin dan Petugas --}}
                                                    <a href="{{ route($detailRoute, $report) }}" class="activity-row-link">
                                                        Detail
                                                    </a>

                                                    {{-- Aksi khusus Petugas --}}
                                                    @unless ($isAdmin)
                                                        @if (
                                                                                in_array(
                                                                                    $report->status,
                                                                                    ['draft', 'revision_needed'],
                                                                                    true
                                                                                )
                                                                            )
                                                                            <a href="{{ route('petugas.reports.edit', $report) }}" class="activity-row-link">
                                                                                {{ $report->status === 'revision_needed'
                                                            ? 'Perbaiki'
                                                            : 'Edit'
                                                                                                                                    }}
                                                                            </a>
                                                        @elseif ($report->status === 'submitted')
                                                            <span class="text-slate-500">
                                                                Menunggu validasi
                                                            </span>
                                                        @else
                                                            <span class="text-slate-500">
                                                                Selesai
                                                            </span>
                                                        @endif

                                                        {{-- Aksi khusus Admin --}}
                                                    @else
                                                        @if ($report->status === 'submitted')
                                                            <span class="text-blue-700">
                                                                Perlu ditinjau
                                                            </span>
                                                        @endif
                                                    @endunless

                                                </div>
                                            </td>


                                        </tr>

                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 6 : 7 }}" class="admin-empty-message">
                                    @if ($search !== '' || $statusFilter !== '')
                                        Tidak ada laporan yang sesuai dengan
                                        pencarian atau filter.
                                    @elseif ($isAdmin)
                                        Belum ada laporan yang tercatat dalam sistem.
                                    @else
                                        Belum ada laporan dari kegiatan
                                        yang ditugaskan kepada Anda.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reports->hasPages())
                <div class="activity-pagination">
                    {{ $reports->links() }}
                </div>
            @endif

        </section>

    </div>
@endsection