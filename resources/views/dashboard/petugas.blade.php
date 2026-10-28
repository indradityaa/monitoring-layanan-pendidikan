@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard Petugas')

@section('content')
    <div class="space-y-6">

        {{-- Heading --}}
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Dashboard Petugas
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Pantau jadwal kegiatan yang menjadi tanggung jawab Anda.
                </p>
            </div>

            <span
                class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700">
                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                Data Database
            </span>
        </div>

        {{-- KPI --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Jadwal Hari Ini --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Hari Ini
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ number_format($summary['today']) }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Jadwal hari ini
                </p>
            </div>

            {{-- Jadwal Mendatang --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Mendatang
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ number_format($summary['upcoming']) }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Jadwal setelah hari ini
                </p>
            </div>

            {{-- Jadwal Selesai --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Selesai
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ number_format($summary['completed']) }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Jadwal berstatus selesai
                </p>
            </div>

            {{-- Laporan --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Laporan
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ number_format($summary['reports']) }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Kegiatan yang memiliki laporan
                </p>
            </div>

        </div>

        {{-- Jadwal kegiatan petugas --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-5 sm:p-6">
                <div>
                    <h2 class="font-semibold text-slate-800">
                        Kegiatan Saya
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Jadwal dari kegiatan yang ditugaskan kepada Anda.
                    </p>
                </div>

                <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                    Maksimal 5 jadwal
                </span>
            </div>

            <div class="divide-y divide-slate-100">

                @forelse ($mySchedules as $schedule)

                        @php
                            $activity = $schedule->activity;

                            $scheduleStatusMeta = [
                                'scheduled' => [
                                    'label' => 'Terjadwal',
                                    'class' => 'bg-blue-50 text-blue-700',
                                ],
                                'rescheduled' => [
                                    'label' => 'Dijadwalkan Ulang',
                                    'class' => 'bg-amber-50 text-amber-700',
                                ],
                                'completed' => [
                                    'label' => 'Selesai',
                                    'class' => 'bg-emerald-50 text-emerald-700',
                                ],
                                'cancelled' => [
                                    'label' => 'Dibatalkan',
                                    'class' => 'bg-red-50 text-red-700',
                                ],
                            ];

                            $scheduleStatus = $scheduleStatusMeta[$schedule->status]
                                ?? [
                                    'label' => ucfirst($schedule->status),
                                    'class' => 'bg-slate-100 text-slate-700',
                                ];

                            $locationLabel = $schedule->location?->city
                                ?? $schedule->location?->district;

                            $report = $activity?->report;
                        @endphp

                        <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">

                            {{-- Informasi kegiatan --}}
                            <div class="min-w-0 flex-1">

                                <p class="font-semibold text-slate-800">
                                    {{ $activity?->name ?? 'Kegiatan tidak tersedia' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Kode:
                                    {{ $activity?->code ?? '—' }}

                                    <span class="mx-1">•</span>

                                    {{ $activity?->activityType?->name ?? 'Jenis tidak tersedia' }}
                                </p>

                                {{-- Tanggal dan waktu --}}
                                <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-600">

                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                                            <path d="M16 2v4M8 2v4M3 10h18"></path>
                                        </svg>

                                        {{ $schedule->schedule_date?->format('d/m/Y') ?? '—' }}
                                    </span>

                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <path d="M12 7v5l3 2"></path>
                                        </svg>

                                        {{ $schedule->start_time
                    ? substr($schedule->start_time, 0, 5)
                    : '—'
                                                        }}
                                        –
                                        {{ $schedule->end_time
                    ? substr($schedule->end_time, 0, 5)
                    : '—'
                                                        }}
                                    </span>
                                </div>

                                {{-- Lokasi --}}
                                <p class="mt-2 text-sm text-slate-500">
                                    Lokasi:
                                    {{ $schedule->location?->name ?? 'Belum tersedia' }}

                                    @if ($locationLabel)
                                        · {{ $locationLabel }}
                                    @endif
                                </p>

                            </div>

                            {{-- Status jadwal dan laporan --}}
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">

                                <span
                                    class="inline-flex rounded-full px-3 py-1.5 text-xs font-medium {{ $scheduleStatus['class'] }}">
                                    {{ $scheduleStatus['label'] }}
                                </span>

                                @if ($report)
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                                        Laporan tersedia
                                    </span>
                                @endif

                            </div>

                        </div>

                @empty

                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                            <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                                <path d="M16 2v4M8 2v4M3 10h18"></path>
                            </svg>
                        </div>

                        <h3 class="mt-4 font-semibold text-slate-700">
                            Belum Ada Jadwal
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Belum ada jadwal kegiatan yang ditugaskan kepada akun Anda.
                        </p>
                    </div>

                @endforelse

            </div>
        </div>

    </div>
@endsection