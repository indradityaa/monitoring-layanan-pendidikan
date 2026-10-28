<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ActivityAssignmentController extends Controller
{
    /**
     * Menampilkan daftar penugasan.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $query = ActivityAssignment::query()
            ->with([
                'activity.activityType',
                'user',
            ]);

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
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        $assignments = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('assignments.index', compact(
            'assignments',
            'search'
        ));
    }

    /**
     * Menampilkan formulir penugasan.
     */
    public function create()
    {
        $activities = Activity::query()
            ->where('status', '!=', 'cancelled')
            ->with('activityType')
            ->orderBy('name')
            ->get();

        $officers = User::query()
            ->where('role', 'petugas')
            ->orderBy('name')
            ->get();

        return view('assignments.create', compact(
            'activities',
            'officers'
        ));
    }

    /**
     * Menyimpan penugasan satu atau beberapa petugas.
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

            'user_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'user_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('users', 'id')
                    ->where(function ($query) {
                        $query->where('role', 'petugas');
                    }),
            ],

            'assignment_roles' => [
                'nullable',
                'array',
            ],

            'assignment_roles.*' => [
                'nullable',
                'string',
                'max:100',
            ],
        ], [
            'activity_id.required' => 'Kegiatan wajib dipilih.',
            'activity_id.exists' => 'Kegiatan tidak valid atau sudah dibatalkan.',
            'user_ids.required' => 'Pilih minimal satu petugas.',
            'user_ids.min' => 'Pilih minimal satu petugas.',
            'user_ids.*.distinct' => 'Petugas yang sama tidak boleh dipilih berulang kali.',
            'user_ids.*.exists' => 'Salah satu akun bukan petugas yang valid.',
            'assignment_roles.*.max' => 'Peran penugasan maksimal 100 karakter.',
        ]);

        $result = DB::transaction(function () use ($validated) {
            /*
             * Mengunci record kegiatan selama proses penyimpanan.
             */
            $activity = Activity::query()
                ->lockForUpdate()
                ->findOrFail($validated['activity_id']);

            if ($activity->status === 'cancelled') {
                abort(
                    422,
                    'Kegiatan sudah dibatalkan dan tidak dapat diberi penugasan.'
                );
            }

            $createdCount = 0;
            $existingCount = 0;

            foreach ($validated['user_ids'] as $userId) {
                $userId = (int) $userId;

                $role = trim((string) (
                    $validated['assignment_roles'][$userId] ?? ''
                ));

                /*
                 * Unik: satu petugas hanya memiliki satu record
                 * penugasan untuk kegiatan yang sama.
                 */
                $assignment = ActivityAssignment::firstOrNew([
                    'activity_id' => $activity->id,
                    'user_id' => $userId,
                ]);

                if (!$assignment->exists) {
                    $assignment->assignment_role =
                        $role !== '' ? $role : null;

                    $assignment->save();

                    $createdCount++;
                } else {
                    /*
                     * Penugasan yang sudah ada tidak dibuat ulang.
                     * Peran lama hanya diperbarui jika peran baru
                     * diisi oleh Admin.
                     */
                    if ($role !== '') {
                        $assignment->assignment_role = $role;
                        $assignment->save();
                    }

                    $existingCount++;
                }
            }

            return [
                'created' => $createdCount,
                'existing' => $existingCount,
            ];
        });

        $message = $result['created']
            . ' penugasan baru berhasil disimpan.';

        if ($result['existing'] > 0) {
            $message .= ' '
                . $result['existing']
                . ' penugasan sudah ada dan tidak dibuat ulang.';
        }

        return redirect()
            ->route('admin.assignments.index')
            ->with('success', $message);
    }

    /**
     * Menampilkan form edit satu penugasan.
     */
    public function edit(ActivityAssignment $assignment)
    {
        $assignment->load([
            'activity.activityType',
            'user',
        ]);

        /*
         * Tampilkan kegiatan yang tidak dibatalkan.
         * Kegiatan yang sedang terkait dengan penugasan ini
         * tetap tersedia agar record lama bisa diedit.
         */
        $activities = Activity::query()
            ->where(function ($query) use ($assignment) {
                $query->where('status', '!=', 'cancelled')
                    ->orWhere('id', $assignment->activity_id);
            })
            ->with('activityType')
            ->orderBy('name')
            ->get();

        /*
         * Tampilkan akun Petugas.
         * Petugas saat ini tetap ditampilkan jika role akunnya
         * pernah berubah setelah penugasan dibuat.
         */
        $officers = User::query()
            ->where(function ($query) use ($assignment) {
                $query->where('role', 'petugas')
                    ->orWhere('id', $assignment->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('assignments.edit', compact(
            'assignment',
            'activities',
            'officers'
        ));
    }

    /**
     * Memperbarui satu penugasan.
     */
    public function update(
        Request $request,
        ActivityAssignment $assignment
    ) {
        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
                Rule::exists('activities', 'id')
                    ->where(function ($query) use ($assignment) {
                        $query->where(function ($condition) use ($assignment) {
                            $condition
                                ->where('status', '!=', 'cancelled')
                                ->orWhere('id', $assignment->activity_id);
                        });
                    }),
            ],

            'user_id' => [
                'required',
                'integer',

                Rule::exists('users', 'id')
                    ->where(function ($query) use ($assignment) {
                        $query->where(function ($condition) use ($assignment) {
                            $condition
                                ->where('role', 'petugas')
                                ->orWhere('id', $assignment->user_id);
                        });
                    }),

                /*
                 * Cegah pasangan kegiatan dan petugas yang sama
                 * dibuat dua kali, kecuali record yang sedang diedit.
                 */
                Rule::unique('activity_assignments', 'user_id')
                    ->where(function ($query) use ($request) {
                        $query->where(
                            'activity_id',
                            $request->input('activity_id')
                        );
                    })
                    ->ignore($assignment->id),
            ],

            'assignment_role' => [
                'nullable',
                'string',
                'max:100',
            ],
        ], [
            'activity_id.required' => 'Kegiatan wajib dipilih.',
            'activity_id.exists' =>
                'Kegiatan tidak valid atau sudah dibatalkan.',
            'user_id.required' => 'Petugas wajib dipilih.',
            'user_id.exists' =>
                'Akun yang dipilih bukan petugas yang valid.',
            'user_id.unique' =>
                'Petugas tersebut sudah ditugaskan pada kegiatan yang dipilih.',
            'assignment_role.max' =>
                'Peran penugasan maksimal 100 karakter.',
        ]);

        $assignment->update([
            'activity_id' => $validated['activity_id'],
            'user_id' => $validated['user_id'],
            'assignment_role' => trim(
                (string) ($validated['assignment_role'] ?? '')
            ) ?: null,
        ]);

        return redirect()
            ->route('admin.assignments.index')
            ->with(
                'success',
                'Penugasan berhasil diperbarui.'
            );
    }

}
