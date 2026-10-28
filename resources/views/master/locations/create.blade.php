@extends('layouts.app')

@section('title', 'Tambah Lokasi')
@section('page-title', 'Lokasi')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MASTER DATA</p>

                <h1>Tambah Lokasi</h1>

                <p class="activity-page-description">
                    Tambahkan informasi lokasi pelaksanaan kegiatan.
                </p>
            </div>

            <a href="{{ route('admin.master.locations.index') }}" class="activity-button-secondary">
                Kembali
            </a>
        </section>

        <div class="activity-form-notice">
            <strong>Informasi.</strong>
            Lokasi baru akan langsung berstatus Aktif dan tersedia
            untuk jadwal baru.
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
                    <p>Nama lokasi wajib diisi.</p>
                </div>
            </header>

            <form action="{{ route('admin.master.locations.store') }}" method="POST" class="activity-form">
                @csrf

                {{-- Nama lokasi --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="name">
                        Nama Lokasi <span class="activity-required">*</span>
                    </label>

                    <input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="150" required autofocus
                        placeholder="Contoh: Aula Balai Tekkomdik DIY"
                        class="activity-filter-control @error('name') activity-input-error @enderror">

                    @error('name')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="address">Alamat Lengkap</label>

                    <textarea id="address" name="address" rows="4" maxlength="5000"
                        placeholder="Nama jalan, nomor bangunan, atau petunjuk alamat..."
                        class="activity-filter-control activity-textarea @error('address') activity-input-error @enderror">{{ old('address') }}</textarea>

                    @error('address')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Kecamatan --}}
                <div class="activity-form-field">
                    <label for="district">Kecamatan</label>

                    <input id="district" name="district" type="text" value="{{ old('district') }}" maxlength="100"
                        placeholder="Contoh: Sewon"
                        class="activity-filter-control @error('district') activity-input-error @enderror">

                    @error('district')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Kota atau kabupaten --}}
                <div class="activity-form-field">
                    <label for="city">Kota/Kabupaten</label>

                    <input id="city" name="city" type="text" value="{{ old('city') }}" maxlength="100"
                        placeholder="Contoh: Bantul"
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
                        Simpan Lokasi
                    </button>
                </div>

            </form>
        </section>

    </div>
@endsection