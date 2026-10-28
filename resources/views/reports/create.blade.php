@extends('layouts.app')

@section('title', 'Buat Laporan')
@section('page-title', 'Buat Laporan')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN LAPORAN</p>

                <h1>Buat Laporan</h1>

                <p class="activity-page-description">
                    Buat draft laporan untuk kegiatan yang ditugaskan kepada Anda.
                </p>
            </div>

            <a href="{{ route('petugas.reports.index') }}" class="activity-button-secondary">
                Kembali
            </a>
        </section>

        <div class="activity-form-notice">
            <strong>Informasi.</strong>
            Laporan akan disimpan sebagai Draft. Anda dapat mengeditnya
            sebelum mengirim untuk divalidasi Admin.
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
                    <h2>Informasi Laporan</h2>
                    <p>Pilih kegiatan yang belum memiliki laporan.</p>
                </div>
            </header>

            <form action="{{ route('petugas.reports.store') }}" method="POST" class="activity-form">
                @csrf

                <div class="activity-form-field activity-form-field-full">
                    <label for="activity_id">
                        Kegiatan <span class="activity-required">*</span>
                    </label>

                    <select id="activity_id" name="activity_id" required
                        class="activity-filter-control @error('activity_id') activity-input-error @enderror"
                        @disabled($activities->isEmpty())>
                        <option value="">Pilih kegiatan</option>

                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}" @selected((string) old('activity_id') === (string) $activity->id)>
                                {{ $activity->code }} — {{ $activity->name }}
                                ({{ $activity->activityType?->name ?? 'Tanpa jenis' }})
                            </option>
                        @endforeach
                    </select>

                    @if ($activities->isEmpty())
                        <small class="activity-field-error">
                            Belum ada kegiatan yang dapat dibuatkan laporan.
                            Pastikan Anda sudah ditugaskan pada kegiatan yang
                            belum memiliki laporan dan kegiatannya tidak dibatalkan.
                        </small>
                    @endif

                    @error('activity_id')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field activity-form-field-full">
                    <label for="content">Isi Laporan</label>

                    <textarea id="content" name="content" rows="12" maxlength="50000"
                        placeholder="Tuliskan ringkasan pelaksanaan kegiatan, hasil, kendala, dan tindak lanjut..."
                        class="activity-filter-control activity-textarea @error('content') activity-input-error @enderror">{{ old('content') }}</textarea>

                    <small>
                        Anda dapat menyimpan draft terlebih dahulu dan
                        melengkapinya sebelum pengiriman.
                    </small>

                    @error('content')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-actions">
                    <a href="{{ route('petugas.reports.index') }}" class="activity-button-secondary">
                        Batal
                    </a>

                    <button type="submit" class="activity-button-primary" @disabled($activities->isEmpty())>
                        Simpan Draft
                    </button>
                </div>
            </form>
        </section>

    </div>
@endsection