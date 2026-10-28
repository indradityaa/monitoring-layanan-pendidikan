@extends('layouts.app')

@section('title', 'Tambah Jadwal')
@section('page-title', 'Tambah Jadwal')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN JADWAL</p>

                <h1>Tambah Jadwal</h1>

                <p class="activity-page-description">
                    Tentukan kegiatan, waktu, dan lokasi pelaksanaan.
                </p>
            </div>
        </section>

        <div class="activity-form-notice">
            <strong>Informasi.</strong>
            Pastikan tanggal, waktu, dan lokasi sesuai rencana pelaksanaan.
        </div>

        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Periksa kembali formulir.</strong>
                <p>Beberapa input perlu diperbaiki sebelum jadwal disimpan.</p>
            </div>
        @endif

        <section class="admin-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Informasi Jadwal</h2>
                    <p>Kolom bertanda * wajib diisi.</p>
                </div>
            </header>

            <form action="{{ route('admin.schedules.store') }}" method="POST" class="activity-form">
                @csrf

                {{-- Kegiatan --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="activity_id">
                        Kegiatan <span class="activity-required">*</span>
                    </label>

                    <select id="activity_id" name="activity_id" required
                        class="activity-filter-control @error('activity_id') activity-input-error @enderror"
                        aria-describedby="activity-id-error">
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
                            Belum ada kegiatan yang dapat dijadwalkan.
                            Buat kegiatan terlebih dahulu.
                        </small>
                    @endif

                    @error('activity_id')
                        <small id="activity-id-error" class="activity-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div class="activity-form-field">
                    <label for="schedule_date">
                        Tanggal Pelaksanaan
                        <span class="activity-required">*</span>
                    </label>

                    <input id="schedule_date" name="schedule_date" type="date" value="{{ old('schedule_date') }}"
                        min="{{ now()->toDateString() }}" required
                        class="activity-filter-control @error('schedule_date') activity-input-error @enderror">

                    @error('schedule_date')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Lokasi --}}
                <div class="activity-form-field">
                    <label for="location_name">
                        Lokasi <span class="activity-required">*</span>
                    </label>

                    <input id="location_name" name="location_name" type="text" value="{{ old('location_name') }}"
                        maxlength="150" required placeholder="Contoh: Aula Balai Tekkomdik DIY"
                        class="activity-filter-control @error('location_name') activity-input-error @enderror"
                        aria-describedby="location-name-help location-name-error">

                    <small id="location-name-help">
                        Ketik nama lokasi secara langsung. Lokasi yang belum tersedia
                        akan disimpan sebagai lokasi baru.
                    </small>

                    @error('location_name')
                        <small id="location-name-error" class="activity-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>


                {{-- Waktu mulai --}}
                <div class="activity-form-field">
                    <label for="start_time">
                        Waktu Mulai <span class="activity-required">*</span>
                    </label>

                    <input id="start_time" name="start_time" type="time" value="{{ old('start_time') }}" required
                        class="activity-filter-control @error('start_time') activity-input-error @enderror">

                    @error('start_time')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Waktu selesai --}}
                <div class="activity-form-field">
                    <label for="end_time">
                        Waktu Selesai <span class="activity-required">*</span>
                    </label>

                    <input id="end_time" name="end_time" type="time" value="{{ old('end_time') }}" required
                        class="activity-filter-control @error('end_time') activity-input-error @enderror">

                    @error('end_time')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="activity-form-field">
                    <label for="status">
                        Status Jadwal <span class="activity-required">*</span>
                    </label>

                    <select id="status" name="status" required
                        class="activity-filter-control @error('status') activity-input-error @enderror">
                        <option value="scheduled" @selected(old('status', 'scheduled') === 'scheduled')>Terjadwal</option>

                        <option value="rescheduled" @selected(old('status') === 'rescheduled')>Dijadwalkan Ulang</option>

                        <option value="completed" @selected(old('status') === 'completed')>Selesai</option>

                        <option value="cancelled" @selected(old('status') === 'cancelled')>Dibatalkan</option>
                    </select>

                    @error('status')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="notes">Catatan Jadwal</label>

                    <textarea id="notes" name="notes" rows="4" maxlength="5000"
                        placeholder="Tambahkan informasi pendukung jika diperlukan..."
                        class="activity-filter-control activity-textarea @error('notes') activity-input-error @enderror">{{ old('notes') }}</textarea>

                    @error('notes')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="activity-form-actions">
                    <a href="{{ route('admin.schedules.index') }}" class="activity-button-secondary">
                        Batal
                    </a>

                    <button type="submit" class="activity-button-primary" @disabled($activities->isEmpty())>
                        Simpan Jadwal
                    </button>
                </div>

            </form>
        </section>

    </div>
@endsection