@extends('layouts.app')

@section('title', 'Edit Jenis Kegiatan')
@section('page-title', 'Master Data')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MASTER DATA</p>

                <h1>Edit Jenis Kegiatan</h1>

                <p class="activity-page-description">
                    Perbarui informasi {{ $activityType->name }}.
                </p>
            </div>

            <a href="{{ route('admin.master.activity-types.index') }}" class="activity-button-secondary">
                Kembali
            </a>
        </section>

        <div class="activity-form-notice">
            <strong>Status saat ini:</strong>
            {{ $activityType->is_active ? 'Aktif' : 'Nonaktif' }}.

            Perubahan nama atau deskripsi tidak mengubah kegiatan yang
            sudah tersimpan.
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
                    <h2>Informasi Jenis Kegiatan</h2>

                    <p>
                        Digunakan oleh {{ $activityType->activities_count }}
                        kegiatan.
                    </p>
                </div>

                <span class="admin-status {{ $activityType->is_active
        ? 'admin-status-completed'
        : 'admin-status-cancelled'
                    }}">
                    {{ $activityType->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </header>

            <form action="{{ route(
        'admin.master.activity-types.update',
        $activityType
    ) }}" method="POST" class="activity-form">
                @csrf
                @method('PUT')

                <div class="activity-form-field activity-form-field-full">
                    <label for="name">
                        Nama Jenis Kegiatan
                        <span class="activity-required">*</span>
                    </label>

                    <input id="name" name="name" type="text" value="{{ old('name', $activityType->name) }}" maxlength="100"
                        required class="activity-filter-control @error('name') activity-input-error @enderror">

                    @error('name')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field activity-form-field-full">
                    <label for="description">Deskripsi</label>

                    <textarea id="description" name="description" rows="5" maxlength="5000"
                        class="activity-filter-control activity-textarea @error('description') activity-input-error @enderror">{{ old('description', $activityType->description) }}</textarea>

                    @error('description')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-actions">
                    <a href="{{ route('admin.master.activity-types.index') }}" class="activity-button-secondary">
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