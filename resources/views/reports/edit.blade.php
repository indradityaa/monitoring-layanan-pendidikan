@extends('layouts.app')

@section('title', 'Edit Laporan')
@section('page-title', 'Edit Laporan')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN LAPORAN</p>

                <h1>
                    {{ $report->status === 'revision_needed'
        ? 'Perbaiki Laporan'
        : 'Edit Draft Laporan'
                        }}
                </h1>

                <p class="activity-page-description">
                    {{ $report->activity?->name ?? 'Kegiatan tidak tersedia' }}
                </p>
            </div>

            <a href="{{ route('petugas.reports.index') }}" class="activity-button-secondary">
                Kembali
            </a>
        </section>

        {{-- Status laporan --}}
        <div class="activity-form-notice">
            <strong>Status saat ini:</strong>

            {{ $report->status === 'revision_needed'
        ? 'Perlu Revisi'
        : 'Draft'
                }}

            @if ($report->status === 'revision_needed')
                <p class="report-current-revision">
                    Catatan Admin:
                    {{ $report->revision_note ?: 'Tidak ada catatan revisi.' }}
                </p>
            @else
                Laporan belum dikirim untuk divalidasi Admin.
            @endif
        </div>

        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Periksa kembali formulir.</strong>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <section class="admin-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>Isi Laporan</h2>

                    <p>
                        Kode kegiatan:
                        {{ $report->activity?->code ?? '—' }}
                    </p>
                </div>

                <span class="admin-panel-period">
                    ID Laporan #{{ $report->id }}
                </span>
            </header>

            <form action="{{ route('petugas.reports.update', $report) }}" method="POST" class="activity-form">
                @csrf
                @method('PUT')

                <div class="activity-form-field activity-form-field-full">
                    <label for="content">Isi Laporan</label>

                    <textarea id="content" name="content" rows="12" maxlength="50000"
                        placeholder="Tuliskan ringkasan pelaksanaan, hasil kegiatan, kendala, dan tindak lanjut..."
                        class="activity-filter-control activity-textarea @error('content') activity-input-error @enderror">{{ old('content', $report->content) }}</textarea>

                    <small>
                        Perubahan disimpan pada laporan yang sama.
                        Status saat ini tetap dipertahankan sampai laporan dikirim
                        kembali atau diproses Admin.
                    </small>

                    @error('content')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-actions">
                    <a href="{{ route('petugas.reports.index') }}" class="activity-button-secondary">
                        Batal
                    </a>

                    <button type="submit" class="activity-button-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

    </div>
@endsection