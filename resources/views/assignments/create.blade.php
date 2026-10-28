@extends('layouts.app')

@section('title', 'Tambah Penugasan')
@section('page-title', 'Tambah Penugasan')

@section('content')
    <div class="activity-page">

        <section class="activity-page-heading">
            <div>
                <p class="admin-eyebrow">MANAJEMEN PENUGASAN</p>

                <h1>Tambah Penugasan</h1>

                <p class="activity-page-description">
                    Pilih kegiatan dan tentukan petugas yang terlibat.
                </p>
            </div>
        </section>

        <div class="activity-form-notice">
            <strong>Informasi.</strong>
            Petugas yang sudah ditugaskan pada kegiatan yang sama tidak akan
            dibuatkan record penugasan duplikat.
        </div>

        @if ($errors->any())
            <div class="activity-error-summary" role="alert">
                <strong>Periksa kembali formulir.</strong>
                <p>
                    {{ $errors->first() }}
                </p>
            </div>
        @endif

        <section class="admin-panel">

            <header class="admin-panel-header">
                <div>
                    <h2>Informasi Penugasan</h2>

                    <p>
                        Pilih satu kegiatan dan minimal satu petugas.
                    </p>
                </div>
            </header>

            <form action="{{ route('admin.assignments.store') }}" method="POST" class="assignment-form">
                @csrf

                {{-- Pilih kegiatan --}}
                <div class="activity-form-field">
                    <label for="activity_id">
                        Kegiatan <span class="activity-required">*</span>
                    </label>

                    <select id="activity_id" name="activity_id" required
                        class="activity-filter-control @error('activity_id') activity-input-error @enderror">
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
                            Belum ada kegiatan yang dapat diberi penugasan.
                        </small>
                    @endif

                    @error('activity_id')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Pilih petugas --}}
                <div class="activity-form-field">
                    <div class="assignment-section-heading">
                        <label>
                            Petugas <span class="activity-required">*</span>
                        </label>

                        <span class="assignment-section-hint">
                            Pilih satu atau beberapa
                        </span>
                    </div>

                    @if ($officers->isEmpty())
                        <div class="assignment-empty-officers">
                            Belum ada akun dengan role Petugas.
                        </div>
                    @else
                        @php
                            $oldUserIds = array_map(
                                'strval',
                                (array) old('user_ids', [])
                            );
                        @endphp

                        <div class="assignment-officer-list">
                            @foreach ($officers as $officer)
                                <div class="assignment-officer-row">

                                    <label for="officer-{{ $officer->id }}" class="assignment-officer-check">
                                        <input id="officer-{{ $officer->id }}" type="checkbox" name="user_ids[]"
                                            value="{{ $officer->id }}" @checked(
                                                in_array(
                                                    (string) $officer->id,
                                                    $oldUserIds,
                                                    true
                                                )
                                            )>

                                        <span class="assignment-officer-avatar">
                                            {{ strtoupper(substr($officer->name, 0, 1)) }}
                                        </span>

                                        <span class="assignment-officer-info">
                                            <strong>{{ $officer->name }}</strong>
                                            <small>{{ $officer->email }}</small>
                                        </span>
                                    </label>

                                    <div class="assignment-role-field">
                                        <label for="role-{{ $officer->id }}">
                                            Peran
                                        </label>

                                        <input id="role-{{ $officer->id }}" type="text" name="assignment_roles[{{ $officer->id }}]"
                                            value="{{ old('assignment_roles.' . $officer->id) }}" maxlength="100"
                                            placeholder="Misal: Pendamping" class="activity-filter-control">
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <small>
                            Peran bersifat opsional. Jika dikosongkan untuk
                            penugasan baru, peran akan disimpan sebagai
                            belum ditentukan.
                        </small>
                    @endif

                    @error('user_ids')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror

                    @error('user_ids.*')
                        <small class="activity-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="activity-form-actions">
                    <a href="{{ route('admin.assignments.index') }}" class="activity-button-secondary">
                        Batal
                    </a>

                    <button type="submit" class="activity-button-primary" @disabled($activities->isEmpty() || $officers->isEmpty())>
                        Simpan Penugasan
                    </button>
                </div>

            </form>
        </section>

    </div>
@endsection