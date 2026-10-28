@extends('layouts.app')

@section('title', 'Edit Penugasan')
@section('page-title', 'Edit Penugasan')

@section('content')
    <div class="activity-page">

        {{-- Heading --}}
        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN PENUGASAN</p>

                <h1>Edit Penugasan</h1>

                <p class="activity-page-description">
                    Perbarui kegiatan, petugas, atau peran penugasan.
                </p>
            </div>

            <a href="{{ route('admin.assignments.index') }}" class="activity-button-secondary">
                Kembali
            </a>
        </section>

        <div class="activity-form-notice">
            <strong>Informasi.</strong>
            Perubahan akan memperbarui penugasan yang dipilih.
            Sistem tidak akan membuat record duplikat.
        </div>

        {{-- Error validasi --}}
        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Periksa kembali formulir.</strong>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        {{-- Informasi penugasan --}}
        <section class="admin-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Informasi Penugasan</h2>

                    <p>
                        Penugasan dibuat pada
                        {{ $assignment->created_at?->format('d/m/Y') ?? '—' }}.
                    </p>
                </div>

                <span class="admin-panel-period">
                    ID #{{ $assignment->id }}
                </span>
            </header>

            <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST" class="activity-form">
                @csrf
                @method('PUT')

                {{-- Kegiatan --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="activity_id">
                        Kegiatan <span class="activity-required">*</span>
                    </label>

                    <select id="activity_id" name="activity_id" required
                        class="activity-filter-control @error('activity_id') activity-input-error @enderror">
                        <option value="">Pilih kegiatan</option>

                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}" @selected(
                                (string) old(
                                    'activity_id',
                                    $assignment->activity_id
                                ) === (string) $activity->id
                            )>
                                {{ $activity->code }} — {{ $activity->name }}
                                ({{ $activity->activityType?->name ?? 'Tanpa jenis' }})
                            </option>
                        @endforeach
                    </select>

                    @error('activity_id')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Petugas --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="user_id">
                        Petugas <span class="activity-required">*</span>
                    </label>

                    <select id="user_id" name="user_id" required
                        class="activity-filter-control @error('user_id') activity-input-error @enderror">
                        <option value="">Pilih petugas</option>

                        @foreach ($officers as $officer)
                            <option value="{{ $officer->id }}" @selected(
                                (string) old(
                                    'user_id',
                                    $assignment->user_id
                                ) === (string) $officer->id
                            )>
                                {{ $officer->name }}
                                — {{ $officer->email }}
                            </option>
                        @endforeach
                    </select>

                    @error('user_id')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Peran --}}
                <div class="activity-form-field activity-form-field-full">
                    <label for="assignment_role">
                        Peran Penugasan
                    </label>

                    <input id="assignment_role" name="assignment_role" type="text" maxlength="100" value="{{ old(
        'assignment_role',
        $assignment->assignment_role
    ) }}" placeholder="Contoh: Narasumber atau Pendamping"
                        class="activity-filter-control @error('assignment_role') activity-input-error @enderror">

                    @error('assignment_role')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror

                    <small>
                        Kosongkan jika peran belum ditentukan.
                    </small>
                </div>

                {{-- Tombol aksi --}}
                <div class="activity-form-actions">
                    <a href="{{ route('admin.assignments.index') }}" class="activity-button-secondary">
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