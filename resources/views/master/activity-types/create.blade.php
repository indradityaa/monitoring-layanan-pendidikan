@extends('layouts.app')

@section('title', 'Tambah Jenis Kegiatan')
@section('page-title', 'Master Data')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MASTER DATA</p>

                <h1>Tambah Jenis Kegiatan</h1>

                <p class="activity-page-description">
                    Tambahkan kategori untuk kegiatan layanan pendidikan.
                </p>
            </div>

            <a href="{{ route('admin.master.activity-types.index') }}" class="activity-button-secondary">
                Kembali
            </a>
        </section>

        <div class="activity-form-notice">
            <strong>Informasi.</strong>
            Jenis kegiatan baru akan langsung berstatus Aktif dan dapat
            dipilih pada formulir kegiatan.
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
                    <p>Nama wajib diisi dan tidak boleh duplikat.</p>
                </div>
            </header>

            <form action="{{ route('admin.master.activity-types.store') }}" method="POST" class="activity-form">
                @csrf

                <div class="activity-form-field activity-form-field-full">
                    <label for="name">
                        Nama Jenis Kegiatan
                        <span class="activity-required">*</span>
                    </label>

                    <input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="100" required autofocus
                        placeholder="Contoh: Workshop"
                        class="activity-filter-control @error('name') activity-input-error @enderror">

                    @error('name')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field activity-form-field-full">
                    <label for="description">Deskripsi</label>

                    <textarea id="description" name="description" rows="5" maxlength="5000"
                        placeholder="Jelaskan fungsi atau cakupan jenis kegiatan ini..."
                        class="activity-filter-control activity-textarea @error('description') activity-input-error @enderror">{{ old('description') }}</textarea>

                    @error('description')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-actions">
                    <a href="{{ route('admin.master.activity-types.index') }}" class="activity-button-secondary">
                        Batal
                    </a>

                    <button type="submit" class="activity-button-primary">
                        Simpan Jenis
                    </button>
                </div>
            </form>
        </section>

    </div>
@endsection