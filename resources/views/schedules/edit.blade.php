@extends('layouts.app')

@section('title', 'Edit Jadwal')
@section('page-title', 'Edit Jadwal')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN JADWAL</p>

                <h1>Edit Jadwal</h1>

                <p class="activity-page-description">
                    Perbarui jadwal pelaksanaan kegiatan.
                </p>
            </div>

            <a href="{{ route('admin.schedules.index') }}" class="activity-button-secondary">
                Kembali ke Jadwal
            </a>
        </section>

        <div class="activity-form-notice">
            <strong>Informasi.</strong>
            Jika tanggal atau waktu berubah, status jadwal dapat berubah menjadi
            Dijadwalkan Ulang sesuai aturan sistem. Status kegiatan induk tidak
            diubah otomatis.
        </div>

        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Periksa kembali formulir.</strong>
                <p>Ada input yang perlu diperbaiki sebelum perubahan disimpan.</p>
            </div>
        @endif

        <section class="admin-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Informasi Jadwal</h2>
                    <p>Kegiatan induk tidak diubah melalui formulir ini.</p>
                </div>

                <span class="admin-panel-period">
                    ID Jadwal #{{ $schedule->id }}
                </span>
            </header>

            <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="activity-form">
                @csrf
                @method('PUT')

                {{-- Kegiatan induk: ditampilkan, tidak dapat diganti --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="activity_display">Kegiatan</label>

                    <input id="activity_display" type="text"
                        value="{{ $schedule->activity?->code }} — {{ $schedule->activity?->name }}"
                        class="activity-filter-control" readonly>
                </div>

                {{-- Tanggal --}}
                <div class="activity-form-field">
                    <label for="schedule_date">
                        Tanggal Pelaksanaan
                        <span class="activity-required">*</span>
                    </label>

                    <input id="schedule_date" name="schedule_date" type="date"
                        value="{{ old('schedule_date', $schedule->schedule_date?->format('Y-m-d')) }}" @if (
                                $schedule->schedule_date
                                && $schedule->schedule_date->format('Y-m-d') >= now()->toDateString()
                            )
                        min="{{ now()->toDateString() }}" @endif required
                        class="activity-filter-control @error('schedule_date') activity-input-error @enderror">

                    <small>
                        Jika tanggal tidak berubah, jadwal lampau masih dapat diedit.
                        Tanggal pengganti harus hari ini atau setelahnya.
                    </small>

                    @error('schedule_date')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Lokasi berupa input teks --}}
                <div class="activity-form-field">
                    <label for="location_name">
                        Lokasi <span class="activity-required">*</span>
                    </label>

                    <input id="location_name" name="location_name" type="text"
                        value="{{ old('location_name', $schedule->location?->name) }}" maxlength="150" required
                        placeholder="Contoh: Aula Pelatihan Baru"
                        class="activity-filter-control @error('location_name') activity-input-error @enderror">

                    <small>
                        Ketik nama lokasi. Lokasi yang belum tersedia akan
                        disimpan sebagai lokasi baru.
                    </small>

                    @error('location_name')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Waktu mulai --}}
                <div class="activity-form-field">
                    <label for="start_time">
                        Waktu Mulai <span class="activity-required">*</span>
                    </label>

                    <input id="start_time" name="start_time" type="time"
                        value="{{ old('start_time', substr($schedule->start_time, 0, 5)) }}" required
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

                    <input id="end_time" name="end_time" type="time"
                        value="{{ old('end_time', substr($schedule->end_time, 0, 5)) }}" required
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
                        <option value="scheduled" @selected(old('status', $schedule->status) === 'scheduled')>
                            Terjadwal
                        </option>

                        <option value="rescheduled" @selected(old('status', $schedule->status) === 'rescheduled')>
                            Dijadwalkan Ulang
                        </option>

                        <option value="completed" @selected(old('status', $schedule->status) === 'completed')>
                            Selesai
                        </option>

                        <option value="cancelled" @selected(old('status', $schedule->status) === 'cancelled')>
                            Dibatalkan
                        </option>
                    </select>

                    @error('status')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="notes">Catatan Jadwal</label>

                    <textarea id="notes" name="notes" rows="4" maxlength="5000"
                        placeholder="Tambahkan catatan jika diperlukan..."
                        class="activity-filter-control activity-textarea @error('notes') activity-input-error @enderror">{{ old('notes', $schedule->notes) }}</textarea>

                    @error('notes')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-actions">
                    <a href="{{ route('admin.schedules.index') }}" class="activity-button-secondary">
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