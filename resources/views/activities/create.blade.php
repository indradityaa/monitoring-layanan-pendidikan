
@extends('layouts.app')

@section('title', 'Tambah Kegiatan')
@section('page-title', 'Tambah Kegiatan')

@section('content')
<div class="activity-page">

    <section class="activity-page-heading">
        <div>
            <p class="admin-eyebrow">MANAJEMEN KEGIATAN</p>
            <h1>Tambah Kegiatan</h1>
            <p class="activity-page-description">
                Lengkapi informasi kegiatan layanan pendidikan.
            </p>
        </div>
    </section>

    <div class="activity-form-notice">
        <strong>Informasi:</strong>
        Kode kegiatan akan dibuat otomatis ketika formulir disimpan.
    </div>

    @if ($errors->any())
        <div class="activity-error-summary" role="alert">
            <strong>Periksa kembali formulir.</strong>
            <p>Terdapat input yang perlu diperbaiki sebelum data disimpan.</p>
        </div>
    @endif

    <section class="admin-panel">

        <header class="admin-panel-header">
            <div>
                <h2>Informasi Kegiatan</h2>
                <p>Kolom bertanda * wajib diisi.</p>
            </div>
        </header>

        <form
            action="{{ route('admin.activities.store') }}"
            method="POST"
            class="activity-form"
        >
            @csrf

            {{-- Preview kode --}}
            <div class="activity-form-field">
                <label for="code-preview">Kode Kegiatan</label>

                <input
                    id="code-preview"
                    type="text"
                    value="{{ $nextCode }}"
                    class="activity-filter-control"
                    readonly
                >

                <small>
                    Preview saja. Kode final ditentukan oleh server saat disimpan.
                </small>
            </div>

            {{-- Nama --}}
            <div class="activity-form-field">
                <label for="name">
                    Nama Kegiatan <span class="activity-required">*</span>
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    maxlength="200"
                    required
                    autofocus
                    placeholder="Contoh: Bimtek Pemanfaatan AI"
                    class="activity-filter-control @error('name') activity-input-error @enderror"
                    aria-describedby="name-error"
                >

                @error('name')
                    <small id="name-error" class="activity-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Jenis --}}
            <div class="activity-form-field">
                <label for="activity_type_id">
                    Jenis Kegiatan <span class="activity-required">*</span>
                </label>

                <select
                    id="activity_type_id"
                    name="activity_type_id"
                    required
                    class="activity-filter-control @error('activity_type_id') activity-input-error @enderror"
                    aria-describedby="type-error"
                >
                    <option value="">Pilih jenis kegiatan</option>

                    @foreach ($activityTypes as $type)
                        <option
                            value="{{ $type->id }}"
                            @selected((string) old('activity_type_id') === (string) $type->id)
                        >
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>

                @if ($activityTypes->isEmpty())
                    <small class="activity-field-error">
                        Belum ada jenis kegiatan aktif. Tambahkan jenis kegiatan
                        terlebih dahulu melalui modul Master Data.
                    </small>
                @endif

                @error('activity_type_id')
                    <small id="type-error" class="activity-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Penanggung jawab --}}
            <div class="activity-form-field">
                <label for="person_in_charge_id">
                    Penanggung Jawab
                </label>

                <select
                    id="person_in_charge_id"
                    name="person_in_charge_id"
                    class="activity-filter-control @error('person_in_charge_id') activity-input-error @enderror"
                    aria-describedby="officer-error"
                >
                    <option value="">Belum ditentukan</option>

                    @foreach ($officers as $officer)
                        <option
                            value="{{ $officer->id }}"
                            @selected((string) old('person_in_charge_id') === (string) $officer->id)
                        >
                            {{ $officer->name }}
                        </option>
                    @endforeach
                </select>

                @error('person_in_charge_id')
                    <small id="officer-error" class="activity-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Target peserta --}}
            <div class="activity-form-field">
                <label for="target_participants">
                    Target Peserta
                </label>

                <input
                    id="target_participants"
                    name="target_participants"
                    type="number"
                    min="1"
                    max="4294967295"
                    step="1"
                    value="{{ old('target_participants') }}"
                    placeholder="Contoh: 25"
                    class="activity-filter-control @error('target_participants') activity-input-error @enderror"
                    aria-describedby="participants-error"
                >

                @error('target_participants')
                    <small id="participants-error" class="activity-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Status --}}
            <div class="activity-form-field">
                <label for="status">
                    Status <span class="activity-required">*</span>
                </label>

                <select
                    id="status"
                    name="status"
                    required
                    class="activity-filter-control @error('status') activity-input-error @enderror"
                    aria-describedby="status-error"
                >
                    <option value="planned"
                        @selected(old('status', 'planned') === 'planned')>
                        Direncanakan
                    </option>

                    <option value="scheduled"
                        @selected(old('status') === 'scheduled')>
                        Terjadwal
                    </option>

                    <option value="ongoing"
                        @selected(old('status') === 'ongoing')>
                        Berlangsung
                    </option>

                    <option value="completed"
                        @selected(old('status') === 'completed')>
                        Selesai
                    </option>

                    <option value="cancelled"
                        @selected(old('status') === 'cancelled')>
                        Dibatalkan
                    </option>
                </select>

                @error('status')
                    <small id="status-error" class="activity-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="activity-form-field activity-form-field-full">
                <label for="description">Deskripsi Kegiatan</label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Jelaskan tujuan atau gambaran singkat kegiatan..."
                    class="activity-filter-control activity-textarea @error('description') activity-input-error @enderror"
                    aria-describedby="description-error"
                >{{ old('description') }}</textarea>

                @error('description')
                    <small id="description-error" class="activity-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <div class="activity-form-actions">
                <a
                    href="{{ route('admin.activities.index') }}"
                    class="activity-button-secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="activity-button-primary"
                    @disabled($activityTypes->isEmpty())
                >
                    Simpan Kegiatan
                </button>
            </div>

        </form>
    </section>

</div>
@endsection
