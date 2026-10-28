@extends('layouts.app')

@section('title', 'Edit Kegiatan')
@section('page-title', 'Edit Kegiatan')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN KEGIATAN</p>
                <h1>Edit Kegiatan</h1>
                <p class="activity-page-description">
                    Perbarui informasi kegiatan {{ $activity->code }}.
                </p>
            </div>
        </section>

        <div class="activity-form-notice">
            <strong>Kode kegiatan: {{ $activity->code }}</strong>
            Kode dan pembuat awal tidak akan berubah ketika data diperbarui.
        </div>

        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Periksa kembali formulir.</strong>
                <p>Ada input yang perlu diperbaiki sebelum data diperbarui.</p>
            </div>
        @endif

        <section class="admin-panel">
            <header class="admin-panel-header">
                <div>
                    <h2>Informasi Kegiatan</h2>
                    <p>Kolom bertanda * wajib diisi.</p>
                </div>
            </header>

            <form action="{{ route('admin.activities.update', $activity) }}" method="POST" class="activity-form">
                @csrf
                @method('PUT')

                <div class="activity-form-field">
                    <label for="code-preview">Kode Kegiatan</label>
                    <input id="code-preview" type="text" value="{{ $activity->code }}" class="activity-filter-control"
                        readonly>
                </div>

                <div class="activity-form-field">
                    <label for="name">
                        Nama Kegiatan <span class="activity-required">*</span>
                    </label>

                    <input id="name" name="name" type="text" value="{{ old('name', $activity->name) }}" maxlength="200"
                        required class="activity-filter-control @error('name') activity-input-error @enderror">

                    @error('name')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field">
                    <label for="activity_type_id">
                        Jenis Kegiatan <span class="activity-required">*</span>
                    </label>

                    <select id="activity_type_id" name="activity_type_id" required
                        class="activity-filter-control @error('activity_type_id') activity-input-error @enderror">
                        <option value="">Pilih jenis kegiatan</option>

                        @foreach ($activityTypes as $type)
                            <option value="{{ $type->id }}" @selected(
                                (string) old(
                                    'activity_type_id',
                                    $activity->activity_type_id
                                ) === (string) $type->id
                            )>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('activity_type_id')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field">
                    <label for="person_in_charge_id">Penanggung Jawab</label>

                    <select id="person_in_charge_id" name="person_in_charge_id"
                        class="activity-filter-control @error('person_in_charge_id') activity-input-error @enderror">
                        <option value="">Belum ditentukan</option>

                        @foreach ($officers as $officer)
                            <option value="{{ $officer->id }}" @selected(
                                (string) old(
                                    'person_in_charge_id',
                                    $activity->person_in_charge_id
                                ) === (string) $officer->id
                            )>
                                {{ $officer->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('person_in_charge_id')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field">
                    <label for="target_participants">Target Peserta</label>

                    <input id="target_participants" name="target_participants" type="number" min="1" max="4294967295"
                        step="1" value="{{ old(
        'target_participants',
        $activity->target_participants
    ) }}" class="activity-filter-control @error('target_participants') activity-input-error @enderror">

                    @error('target_participants')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field">
                    <label for="status">
                        Status <span class="activity-required">*</span>
                    </label>

                    <select id="status" name="status" required
                        class="activity-filter-control @error('status') activity-input-error @enderror">
                        <option value="planned" @selected(old('status', $activity->status) === 'planned')>Direncanakan
                        </option>

                        <option value="scheduled" @selected(old('status', $activity->status) === 'scheduled')>Terjadwal
                        </option>

                        <option value="ongoing" @selected(old('status', $activity->status) === 'ongoing')>Berlangsung</option>

                        <option value="completed" @selected(old('status', $activity->status) === 'completed')>Selesai</option>

                        <option value="cancelled" @selected(old('status', $activity->status) === 'cancelled')>Dibatalkan
                        </option>
                    </select>

                    @error('status')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-field activity-form-field-full">
                    <label for="description">Deskripsi Kegiatan</label>

                    <textarea id="description" name="description" rows="5"
                        class="activity-filter-control activity-textarea @error('description') activity-input-error @enderror">{{ old('description', $activity->description) }}</textarea>

                    @error('description')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="activity-form-actions">
                    <a href="{{ route('admin.activities.show', $activity) }}" class="activity-button-secondary">
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