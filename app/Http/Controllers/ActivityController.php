<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use App\Models\Activity;
use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $allowedStatuses = [
            'planned',
            'scheduled',
            'ongoing',
            'completed',
            'cancelled',
        ];

        $statusFilter = (string) $request->query('status', '');

        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = '';
        }

        $typeFilter = (string) $request->query('activity_type_id', '');

        if ($typeFilter !== '' && !ctype_digit($typeFilter)) {
            $typeFilter = '';
        }

        $query = Activity::query()
            ->with([
                'activityType',
                'personInCharge',
            ])
            ->withCount('schedules');

        /*
         * Pencarian berdasarkan kode atau nama kegiatan.
         */
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        /*
         * Filter status kegiatan.
         */
        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        /*
         * Filter jenis kegiatan.
         */
        if ($typeFilter !== '') {
            $query->where('activity_type_id', $typeFilter);
        }

        $activities = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $activityTypes = ActivityType::query()
            ->orderBy('name')
            ->get();

        $statusMeta = [
            'planned' => [
                'label' => 'Direncanakan',
                'class' => 'admin-status-planned',
            ],
            'scheduled' => [
                'label' => 'Terjadwal',
                'class' => 'admin-status-scheduled',
            ],
            'ongoing' => [
                'label' => 'Berlangsung',
                'class' => 'admin-status-ongoing',
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'admin-status-completed',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'admin-status-cancelled',
            ],
        ];

        return view('activities.index', compact(
            'activities',
            'activityTypes',
            'statusMeta',
            'search',
            'statusFilter',
            'typeFilter'
        ));
    }


    public function create()
    {
        $activityTypes = ActivityType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $officers = User::query()
            ->where('role', 'petugas')
            ->orderBy('name')
            ->get();

        $nextCode = $this->generateActivityCode();

        return view('activities.create', compact(
            'activityTypes',
            'officers',
            'nextCode'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:200',
            ],

            'activity_type_id' => [
                'required',
                'integer',
                Rule::exists('activity_types', 'id')
                    ->where('is_active', true),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'target_participants' => [
                'nullable',
                'integer',
                'min:1',
                'max:4294967295',
            ],

            'person_in_charge_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'petugas'),
            ],

            'status' => [
                'required',
                'in:planned,scheduled,ongoing,completed,cancelled',
            ],
        ], [
            'name.required' => 'Nama kegiatan wajib diisi.',
            'name.max' => 'Nama kegiatan maksimal 200 karakter.',
            'activity_type_id.required' => 'Jenis kegiatan wajib dipilih.',
            'activity_type_id.exists' => 'Jenis kegiatan tidak valid atau tidak aktif.',
            'target_participants.integer' => 'Target peserta harus berupa angka bulat.',
            'target_participants.min' => 'Target peserta minimal 1 orang.',
            'person_in_charge_id.exists' => 'Penanggung jawab harus merupakan akun Petugas yang valid.',
            'status.required' => 'Status kegiatan wajib dipilih.',
            'status.in' => 'Status kegiatan tidak valid.',
        ]);

        $activity = Activity::create([
            'code' => $this->generateActivityCode(),
            'name' => $validated['name'],
            'activity_type_id' => $validated['activity_type_id'],
            'description' => $validated['description'] ?? null,
            'target_participants' =>
                $validated['target_participants'] ?? null,
            'created_by' => $request->user()->id,
            'person_in_charge_id' =>
                $validated['person_in_charge_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.activities.index')
            ->with(
                'success',
                "Kegiatan {$activity->code} berhasil ditambahkan."
            );
    }

    private function generateActivityCode(): string
    {
        $codes = Activity::query()
            ->where('code', 'like', 'KGT-%')
            ->pluck('code');

        $maxNumber = $codes
            ->map(function ($code) {
                if (preg_match('/^KGT-(\d+)$/', $code, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        $nextNumber = $maxNumber + 1;

        do {
            $code = sprintf('KGT-%03d', $nextNumber);
            $nextNumber++;
        } while (Activity::where('code', $code)->exists());

        return $code;
    }


    public function show(Activity $activity)
    {
        $activity->load([
            'activityType',
            'creator',
            'personInCharge',
            'schedules.location',
            'report',
        ]);

        $statusMeta = [
            'planned' => [
                'label' => 'Direncanakan',
                'class' => 'admin-status-planned',
            ],
            'scheduled' => [
                'label' => 'Terjadwal',
                'class' => 'admin-status-scheduled',
            ],
            'ongoing' => [
                'label' => 'Berlangsung',
                'class' => 'admin-status-ongoing',
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'admin-status-completed',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'admin-status-cancelled',
            ],
        ];

        return view('activities.show', compact(
            'activity',
            'statusMeta'
        ));
    }

    public function edit(Activity $activity)
    {
        $activityTypes = ActivityType::query()
            ->where('is_active', true)
            ->orWhere('id', $activity->activity_type_id)
            ->orderBy('name')
            ->get();

        $officersQuery = User::query()
            ->where('role', 'petugas');

        if ($activity->person_in_charge_id !== null) {
            $officersQuery->orWhere(
                'id',
                $activity->person_in_charge_id
            );
        }

        $officers = $officersQuery
            ->orderBy('name')
            ->get();

        return view('activities.edit', compact(
            'activity',
            'activityTypes',
            'officers'
        ));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:200',
            ],

            'activity_type_id' => [
                'required',
                'integer',
                Rule::exists('activity_types', 'id')
                    ->where(function ($query) use ($activity) {
                        $query->where('is_active', true)
                            ->orWhere('id', $activity->activity_type_id);
                    }),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'target_participants' => [
                'nullable',
                'integer',
                'min:1',
                'max:4294967295',
            ],

            'person_in_charge_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'petugas'),
            ],

            'status' => [
                'required',
                'in:planned,scheduled,ongoing,completed,cancelled',
            ],
        ], [
            'name.required' => 'Nama kegiatan wajib diisi.',
            'name.max' => 'Nama kegiatan maksimal 200 karakter.',
            'activity_type_id.required' => 'Jenis kegiatan wajib dipilih.',
            'activity_type_id.exists' => 'Jenis kegiatan tidak valid atau tidak aktif.',
            'target_participants.integer' => 'Target peserta harus berupa angka bulat.',
            'target_participants.min' => 'Target peserta minimal 1 orang.',
            'person_in_charge_id.exists' => 'Penanggung jawab harus merupakan akun Petugas yang valid.',
            'status.required' => 'Status kegiatan wajib dipilih.',
            'status.in' => 'Status kegiatan tidak valid.',
        ]);

        $activity->update([
            // Kode dan pembuat awal tidak diubah.
            'name' => $validated['name'],
            'activity_type_id' => $validated['activity_type_id'],
            'description' => $validated['description'] ?? null,
            'target_participants' =>
                $validated['target_participants'] ?? null,
            'person_in_charge_id' =>
                $validated['person_in_charge_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.activities.show', $activity)
            ->with(
                'success',
                "Kegiatan {$activity->code} berhasil diperbarui."
            );
    }


}
