@extends('layouts.app')

@section('title', 'Edit Lokasi')
@section('page-title', 'Lokasi')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MASTER DATA</p>

                <h1>Edit Lokasi</h1>

                <p class="activity-page-description">
                    Perbarui informasi {{ $location->name }}.
                </p>
            </div>

            <a href="{{ route('admin.master.locations.index') }}" class="activity-button-secondary">
                Kembali
            </a>
        </section>

        <div class="activity-form-notice">
            <strong>Status saat ini:</strong>
            {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}.
            Informasi lokasi dapat diperbarui tanpa membuat jadwal baru.
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
                    <h2>Informasi Lokasi</h2>

                    <p>
                        Digunakan oleh
                        {{ number_format($location->schedules_count) }}
                        jadwal.
                    </p>
                </div>

                <span class="admin-status {{ $location->is_active
        ? 'admin-status-completed'
        : 'admin-status-cancelled'
                    }}">
                    {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </header>

            <form action="{{ route('admin.master.locations.update', $location) }}" method="POST" class="activity-form">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="name">
                        Nama Lokasi <span class="activity-required">*</span>
                    </label>

                    <input id="name" name="name" type="text" value="{{ old('name', $location->name) }}" maxlength="150"
                        required class="activity-filter-control @error('name') activity-input-error @enderror">

                    @error('name')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="address">Alamat Lengkap</label>

                    <textarea id="address" name="address" rows="4" maxlength="5000"
                        class="activity-filter-control activity-textarea @error('address') activity-input-error @enderror"
                        placeholder="Nama jalan, nomor bangunan, atau petunjuk alamat...">{{ old('address', $location->address) }}</textarea>

                    @error('address')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Kecamatan --}}
                <div class="activity-form-field">
                    <label for="district">Kecamatan</label>

                    <input id="district" name="district" type="text" value="{{ old('district', $location->district) }}"
                        maxlength="100" class="activity-filter-control @error('district') activity-input-error @enderror">

                    @error('district')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Kota/kabupaten --}}
                <div class="activity-form-field">
                    <label for="city">Kota/Kabupaten</label>

                    <input id="city" name="city" type="text" value="{{ old('city', $location->city) }}" maxlength="100"
                        class="activity-filter-control @error('city') activity-input-error @enderror">

                    @error('city')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-actions">
                    <a href="{{ route('admin.master.locations.index') }}" class="activity-button-secondary">
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