@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')

@section('content')
    <div class="activity-page">

        {{-- Heading --}}
        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN LAPORAN</p>

                <h1>Detail Laporan</h1>

                <p class="activity-page-description">
                    {{ $report->activity?->name ?? 'Kegiatan tidak tersedia' }}
                </p>
            </div>

            <a href="{{ route(
        $isAdmin ? 'admin.reports.index' : 'petugas.reports.index'
    ) }}" class="activity-button-secondary">
                Kembali ke Daftar
            </a>
        </section>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="activity-success-message" role="status">
                <strong>Berhasil.</strong>
                {{ session('success') }}
            </div>
        @endif

        {{-- Error validasi --}}
        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Periksa kembali tindakan Anda.</strong>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        @php
            $statusMeta = [
                'draft' => [
                    'label' => 'Draft',
                    'class' => 'admin-status-planned',
                ],
                'submitted' => [
                    'label' => 'Menunggu Validasi',
                    'class' => 'admin-status-scheduled',
                ],
                'revision_needed' => [
                    'label' => 'Perlu Revisi',
                    'class' => 'admin-status-ongoing',
                ],
                'validated' => [
                    'label' => 'Tervalidasi',
                    'class' => 'admin-status-completed',
                ],
            ];

            $status = $statusMeta[$report->status] ?? [
                'label' => ucfirst($report->status),
                'class' => 'admin-status-planned',
            ];
        @endphp

        {{-- Ringkasan laporan --}}
        <section class="admin-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>
                        {{ $report->activity?->name ?? 'Kegiatan tidak tersedia' }}
                    </h2>

                    <p>
                        Kode kegiatan:
                        {{ $report->activity?->code ?? '—' }}
                    </p>
                </div>

                <span class="admin-status {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </header>

            <div class="activity-detail-grid">

                <div class="activity-detail-item">
                    <span>Jenis Kegiatan</span>

                    <strong>
                        {{ $report->activity?->activityType?->name ?? '—' }}
                    </strong>
                </div>

                <div class="activity-detail-item">
                    <span>Pengirim Laporan</span>

                    <strong>
                        {{ $report->submitter?->name ?? 'Belum dikirim' }}
                    </strong>
                </div>

                <div class="activity-detail-item">
                    <span>Tanggal Pengiriman</span>

                    <strong>
                        {{ $report->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                    </strong>
                </div>

                <div class="activity-detail-item">
                    <span>Pemeriksa Terakhir</span>

                    <strong>
                        {{ $report->validator?->name ?? 'Belum diperiksa' }}
                    </strong>
                </div>

                <div class="activity-detail-item">
                    <span>Waktu Pemeriksaan</span>

                    <strong>
                        {{ $report->validated_at?->format('d/m/Y H:i') ?? '—' }}
                    </strong>
                </div>

                <div class="activity-detail-item">
                    <span>ID Laporan</span>

                    <strong>#{{ $report->id }}</strong>
                </div>

            </div>
        </section>

        {{-- Isi laporan --}}
        <section class="admin-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>Isi Laporan</h2>
                    <p>Konten yang ditulis oleh Petugas.</p>
                </div>
            </header>

            <div class="report-content">
                @if (filled($report->content))
                    {{ $report->content }}
                @else
                    <span class="text-slate-500">
                        Isi laporan belum tersedia.
                    </span>
                @endif
            </div>
        </section>

        {{-- Catatan revisi --}}
        @if ($report->status === 'revision_needed' && $report->revision_note)
            <section class="admin-panel">
                <header class="admin-panel-header">
                    <div>
                        <h2>Catatan Revisi dari Admin</h2>
                        <p>Perbaiki laporan sesuai catatan berikut.</p>
                    </div>
                </header>

                <div class="report-content report-revision-content">
                    {{ $report->revision_note }}
                </div>
            </section>
        @endif

        {{-- Tindakan Petugas --}}
        @if (
                !$isAdmin &&
                in_array($report->status, ['draft', 'revision_needed'], true)
            )
            <section class="admin-panel">
                <header class="admin-panel-header">
                    <div>
                        <h2>Tindakan Laporan</h2>
                        <p>
                            Periksa kembali isi laporan sebelum mengirimnya
                            untuk divalidasi Admin.
                        </p>
                    </div>
                </header>

                <div class="report-action-panel">

                    <a href="{{ route('petugas.reports.edit', $report) }}" class="activity-button-secondary">
                        {{ $report->status === 'revision_needed'
                ? 'Perbaiki Laporan'
                : 'Edit Draft'
                                    }}
                    </a>

                    @if (filled(trim((string) $report->content)))
                        <form action="{{ route('petugas.reports.submit', $report) }}" method="POST"
                            onsubmit="return confirm('Kirim laporan ini kepada Admin untuk divalidasi?');">
                            @csrf

                            <button type="submit" class="activity-button-primary">
                                {{ $report->status === 'revision_needed'
                        ? 'Kirim Kembali'
                        : 'Kirim untuk Validasi'
                                                    }}
                            </button>
                        </form>
                    @else
                        <p class="report-action-hint">
                            Isi laporan terlebih dahulu sebelum mengirim.
                        </p>
                    @endif

                </div>
            </section>
        @endif

        {{-- Tindakan Admin --}}
        @if ($isAdmin && $report->status === 'submitted')
            <section class="admin-panel">

                <header class="admin-panel-header">
                    <div>
                        <h2>Validasi Laporan</h2>

                        <p>
                            Setujui laporan atau kembalikan kepada Petugas
                            dengan catatan revisi.
                        </p>
                    </div>
                </header>

                <div class="report-review-panel">

                    {{-- Setujui --}}
                    <form action="{{ route('admin.reports.validate', $report) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin menyetujui laporan ini?');">
                        @csrf

                        <button type="submit" class="activity-button-primary">
                            Setujui dan Validasi
                        </button>
                    </form>

                    {{-- Minta revisi --}}
                    <form action="{{ route('admin.reports.revision', $report) }}" method="POST" class="report-revision-form">
                        @csrf

                        <div class="activity-form-field">
                            <label for="revision_note">
                                Catatan Revisi
                                <span class="activity-required">*</span>
                            </label>

                            <textarea id="revision_note" name="revision_note" rows="4" minlength="3" maxlength="5000" required
                                placeholder="Jelaskan bagian yang perlu diperbaiki..."
                                class="activity-filter-control activity-textarea @error('revision_note') activity-input-error @enderror">{{ old('revision_note') }}</textarea>

                            @error('revision_note')
                                <small class="activity-field-error">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <button type="submit" class="activity-button-secondary">
                            Kembalikan untuk Revisi
                        </button>
                    </form>

                </div>
            </section>
        @endif

    </div>
@endsection