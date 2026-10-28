<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Location;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal beserta filter.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'status' => [
                'nullable',
                Rule::in([
                    'scheduled',
                    'rescheduled',
                    'cancelled',
                    'completed',
                ]),
            ],
            'date_from' => ['nullable', 'date'],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $statusFilter = $filters['status'] ?? '';
        $dateFrom = $filters['date_from'] ?? '';
        $dateTo = $filters['date_to'] ?? '';

        $query = Schedule::query()
            ->with([
                'activity.activityType',
                'location',
            ]);

        // Pencarian nama/kode kegiatan atau catatan jadwal.
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->whereHas('activity', function ($activityQuery) use ($search) {
                        $activityQuery->where(function ($nameQuery) use ($search) {
                            $nameQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    })
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($dateFrom !== '') {
            $query->whereDate('schedule_date', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('schedule_date', '<=', $dateTo);
        }

        $today = now()->toDateString();

        // Jadwal hari ini/mendatang ditampilkan terlebih dahulu.
        $schedules = $query
            ->orderByRaw(
                'CASE WHEN schedule_date >= ? THEN 0 ELSE 1 END',
                [$today]
            )
            ->orderByRaw(
                'CASE WHEN schedule_date >= ? THEN schedule_date END ASC',
                [$today]
            )
            ->orderByRaw(
                'CASE WHEN schedule_date < ? THEN schedule_date END DESC',
                [$today]
            )
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        $statusMeta = $this->getStatusMeta();

        return view('schedules.index', compact(
            'schedules',
            'statusMeta',
            'search',
            'statusFilter',
            'dateFrom',
            'dateTo'
        ));


    }

    /**
     * Menampilkan formulir tambah jadwal.
     */
    public function create()
    {
        $activities = Activity::query()
            ->with('activityType')
            ->where('status', '!=', 'cancelled')
            ->orderBy('name')
            ->get();

        return view('schedules.create', compact('activities'));
    }

    /**
     * Memvalidasi dan menyimpan jadwal baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
                Rule::exists('activities', 'id')
                    ->where(function ($query) {
                        $query->where('status', '!=', 'cancelled');
                    }),
            ],

            'schedule_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'location_name' => [
                'required',
                'string',
                'max:150',
            ],

            'status' => [
                'required',
                Rule::in([
                    'scheduled',
                    'rescheduled',
                    'cancelled',
                    'completed',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'activity_id.required' => 'Kegiatan wajib dipilih.',
            'activity_id.exists' => 'Kegiatan tidak valid atau sudah dibatalkan.',
            'schedule_date.required' => 'Tanggal pelaksanaan wajib diisi.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required' => 'Waktu selesai wajib diisi.',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
            'location_name.required' => 'Lokasi wajib diisi.',
            'location_name.max' => 'Nama lokasi maksimal 150 karakter.',
            'status.required' => 'Status jadwal wajib dipilih.',
            'notes.max' => 'Catatan maksimal 5000 karakter.',
            'schedule_date.date_format' => 'Format tanggal pelaksanaan tidak valid.',
            'schedule_date.after_or_equal' => 'Tanggal pelaksanaan tidak boleh sebelum hari ini.',
        ]);

        $location = $this->resolveOrCreateLocation(
            $validated['location_name']
        );

        Schedule::create([
            'activity_id' => $validated['activity_id'],
            'schedule_date' => $validated['schedule_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location_id' => $location->id,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal kegiatan berhasil ditambahkan.');
    }

    /**
     * Label status untuk tampilan.
     */
    private function getStatusMeta(): array
    {
        return [
            'scheduled' => [
                'label' => 'Terjadwal',
                'class' => 'admin-status-scheduled',
            ],
            'rescheduled' => [
                'label' => 'Dijadwalkan Ulang',
                'class' => 'admin-status-ongoing',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'admin-status-cancelled',
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'admin-status-completed',
            ],
        ];
    }


    /**
     * Menampilkan formulir edit jadwal.
     */
    public function edit(Schedule $schedule)
    {
        $schedule->load([
            'activity.activityType',
            'location',
        ]);

        return view('schedules.edit', compact('schedule'));
    }

    /**
     * Memperbarui jadwal atau melakukan penjadwalan ulang.
     */
    public function update(Request $request, Schedule $schedule)
    {
        /*
         * Jika tanggal tidak berubah, jadwal lampau yang sudah ada
         * tetap boleh diedit, misalnya untuk memperbarui catatan.
         *
         * Jika tanggal berubah, tanggal baru harus hari ini
         * atau setelahnya.
         */
        $dateRules = [
            'required',
            'date_format:Y-m-d',
        ];

        $currentDate = $schedule->schedule_date?->format('Y-m-d') ?? '';

        if ($request->input('schedule_date') !== $currentDate) {
            $dateRules[] = 'after_or_equal:today';
        }

        $validated = $request->validate([
            'schedule_date' => $dateRules,

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'location_name' => [
                'required',
                'string',
                'max:150',
            ],

            'status' => [
                'required',
                'in:scheduled,rescheduled,cancelled,completed',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'schedule_date.required' => 'Tanggal pelaksanaan wajib diisi.',
            'schedule_date.date_format' => 'Format tanggal tidak valid.',
            'schedule_date.after_or_equal' =>
                'Tanggal baru tidak boleh sebelum hari ini.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required' => 'Waktu selesai wajib diisi.',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
            'location_name.required' => 'Lokasi wajib diisi.',
            'location_name.max' => 'Nama lokasi maksimal 150 karakter.',
            'status.required' => 'Status jadwal wajib dipilih.',
            'status.in' => 'Status jadwal tidak valid.',
            'notes.max' => 'Catatan maksimal 5000 karakter.',
        ]);

        /*
         * Pembaruan jadwal dan pembuatan lokasi baru dilakukan
         * dalam satu transaksi database.
         */
        $wasRescheduled = DB::transaction(function () use ($schedule, $validated) {
            $currentSchedule = Schedule::query()
                ->with('location')
                ->lockForUpdate()
                ->findOrFail($schedule->id);

            $locationName = trim($validated['location_name']);
            $location = $this->resolveOrCreateLocation(
                $validated['location_name'],
                $currentSchedule->location
            );

            /*
             * Penjadwalan ulang ditentukan oleh perubahan tanggal
             * atau jam. Perubahan lokasi/catatan saja tidak dianggap
             * sebagai penjadwalan ulang.
             */
            $dateTimeChanged =
                $currentSchedule->schedule_date?->format('Y-m-d')
                !== $validated['schedule_date']
                || substr($currentSchedule->start_time, 0, 5)
                !== $validated['start_time']
                || substr($currentSchedule->end_time, 0, 5)
                !== $validated['end_time'];

            $newStatus = $validated['status'];
            $autoRescheduled = false;

            if (
                $dateTimeChanged
                && in_array(
                    $currentSchedule->status,
                    ['scheduled', 'rescheduled'],
                    true
                )
                && in_array(
                    $newStatus,
                    ['scheduled', 'rescheduled'],
                    true
                )
            ) {
                $newStatus = 'rescheduled';
                $autoRescheduled = true;
            }

            $currentSchedule->update([
                'schedule_date' => $validated['schedule_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'location_id' => $location->id,
                'status' => $newStatus,
                'notes' => $validated['notes'] ?? null,
            ]);

            return $autoRescheduled;
        });

        $message = $wasRescheduled
            ? 'Jadwal berhasil diperbarui dan statusnya menjadi Dijadwalkan Ulang.'
            : 'Jadwal berhasil diperbarui.';

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', $message);
    }

    /**
     * Menggunakan lokasi aktif yang sudah ada atau membuat lokasi baru.
     * Nama dibandingkan tanpa membedakan huruf besar dan kecil.
     */
    private function resolveOrCreateLocation(
        string $name,
        ?Location $currentLocation = null
    ): Location {
        $locationName = trim($name);
        $normalizedName = mb_strtolower($locationName);

        $matchingLocations = Location::query()
            ->whereRaw(
                'LOWER(TRIM(name)) = ?',
                [$normalizedName]
            )
            ->orderByDesc('is_active')
            ->get();

        // Jika ada lokasi aktif dengan nama yang sama, gunakan record itu.
        $activeLocation = $matchingLocations->first(
            fn($location) => (bool) $location->is_active
        );

        if ($activeLocation) {
            return $activeLocation;
        }

        /*
         * Jika sedang mengedit jadwal yang sudah menggunakan lokasi
         * nonaktif dan namanya tidak berubah, pertahankan relasi lama.
         */
        if (
            $currentLocation !== null
            && mb_strtolower(trim($currentLocation->name)) === $normalizedName
        ) {
            return $currentLocation;
        }

        // Nama tersebut ada, tetapi seluruh record yang cocok nonaktif.
        if ($matchingLocations->isNotEmpty()) {
            throw ValidationException::withMessages([
                'location_name' =>
                    'Lokasi tersebut sudah terdaftar tetapi nonaktif. '
                    . 'Aktifkan lokasi melalui Master Data sebelum digunakan.',
            ]);
        }

        // Nama benar-benar baru: buat record lokasi.
        return Location::create([
            'name' => $locationName,
            'is_active' => true,
        ]);
    }


}
