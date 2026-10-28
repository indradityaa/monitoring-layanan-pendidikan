<?php

namespace App\Http\Controllers;

use App\Models\ActivityAssignment;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Daftar laporan untuk Admin atau Petugas.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'petugas'], true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman laporan.');
        }

        $isAdmin = $user->role === 'admin';

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'status' => [
                'nullable',
                Rule::in([
                    'draft',
                    'submitted',
                    'revision_needed',
                    'validated',
                ]),
            ],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $statusFilter = $filters['status'] ?? '';

        $query = Report::query()
            ->with([
                'activity.activityType',
                'submitter',
                'validator',
            ]);

        /*
         * Admin melihat semua laporan.
         * Petugas hanya melihat laporan dari kegiatan
         * yang ditugaskan kepadanya.
         */
        if (!$isAdmin) {
            $query->whereHas(
                'activity.assignments',
                function ($assignmentQuery) use ($user) {
                    $assignmentQuery->where('user_id', $user->id);
                }
            );
        }

        /*
         * Cari berdasarkan nama/kode kegiatan atau isi laporan.
         */
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
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        /*
         * Prioritaskan laporan yang menunggu validasi,
         * kemudian laporan yang perlu direvisi.
         */
        $reports = $query
            ->orderByRaw("
                CASE status
                    WHEN 'submitted' THEN 0
                    WHEN 'revision_needed' THEN 1
                    WHEN 'draft' THEN 2
                    WHEN 'validated' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        $statusMeta = [
            'draft' => [
                'label' => 'Draft',
                'class' => 'admin-status-planned',
            ],
            'submitted' => [
                'label' => 'Menunggu Validasi',
                'class' => 'admin-status-scheduled',
            ],
            'revision_needed' => [
                'label' => 'Perlu Revisi',
                'class' => 'admin-status-ongoing',
            ],
            'validated' => [
                'label' => 'Tervalidasi',
                'class' => 'admin-status-completed',
            ],
        ];

        $indexRoute = $isAdmin
            ? 'admin.reports.index'
            : 'petugas.reports.index';
        $detailRoute = $isAdmin
            ? 'admin.reports.show'
            : 'petugas.reports.show';

        return view('reports.index', compact(
            'reports',
            'statusMeta',
            'search',
            'statusFilter',
            'isAdmin',
            'indexRoute',
            'detailRoute',
        ));
    }

    /**
     * Form untuk membuat draft laporan.
     * Hanya kegiatan yang ditugaskan kepada petugas
     * dan belum memiliki laporan yang dapat dipilih.
     */
    public function create(Request $request)
    {
        $user = $request->user();

        abort_unless($user->role === 'petugas', 403);

        $activities = Activity::query()
            ->where('status', '!=', 'cancelled')
            ->whereHas('assignments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDoesntHave('report')
            ->with('activityType')
            ->orderBy('name')
            ->get();

        return view('reports.create', compact('activities'));
    }

    /**
     * Menyimpan laporan baru sebagai draft.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless($user->role === 'petugas', 403);

        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
                'exists:activities,id',
            ],

            'content' => [
                'nullable',
                'string',
                'max:50000',
            ],
        ], [
            'activity_id.required' => 'Kegiatan wajib dipilih.',
            'activity_id.exists' => 'Kegiatan yang dipilih tidak tersedia.',
            'content.max' => 'Isi laporan maksimal 50.000 karakter.',
        ]);

        DB::transaction(function () use ($validated, $user) {
            $activity = Activity::query()
                ->lockForUpdate()
                ->findOrFail($validated['activity_id']);

            if ($activity->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'activity_id' =>
                        'Kegiatan yang dibatalkan tidak dapat dibuatkan laporan.',
                ]);
            }

            $isAssigned = $activity->assignments()
                ->where('user_id', $user->id)
                ->exists();

            if (!$isAssigned) {
                throw ValidationException::withMessages([
                    'activity_id' =>
                        'Anda tidak ditugaskan pada kegiatan ini.',
                ]);
            }

            if ($activity->report()->exists()) {
                throw ValidationException::withMessages([
                    'activity_id' =>
                        'Kegiatan ini sudah memiliki laporan.',
                ]);
            }

            $activity->report()->create([
                'status' => 'draft',
                'content' => $validated['content'] ?? null,
            ]);
        });

        return redirect()
            ->route('petugas.reports.index')
            ->with('success', 'Draft laporan berhasil dibuat.');
    }

    /**
     * Form edit draft atau laporan yang perlu direvisi.
     */
    public function edit(Request $request, Report $report)
    {
        $user = $request->user();

        abort_unless($user->role === 'petugas', 403);

        $report->load('activity.activityType');

        $isAssigned = $report->activity
            && $report->activity->assignments()
                ->where('user_id', $user->id)
                ->exists();

        abort_unless($isAssigned, 403);

        abort_unless(
            in_array($report->status, ['draft', 'revision_needed'], true),
            403,
            'Laporan yang sudah dikirim atau divalidasi tidak dapat diedit.'
        );

        return view('reports.edit', compact('report'));
    }

    /**
     * Memperbarui isi draft tanpa mengubah statusnya.
     */
    public function update(Request $request, Report $report)
    {
        $user = $request->user();

        abort_unless($user->role === 'petugas', 403);

        $report->load('activity');

        $isAssigned = $report->activity
            && $report->activity->assignments()
                ->where('user_id', $user->id)
                ->exists();

        abort_unless($isAssigned, 403);

        abort_unless(
            in_array($report->status, ['draft', 'revision_needed'], true),
            403,
            'Laporan yang sudah dikirim atau divalidasi tidak dapat diedit.'
        );

        $validated = $request->validate([
            'content' => [
                'nullable',
                'string',
                'max:50000',
            ],
        ], [
            'content.max' => 'Isi laporan maksimal 50.000 karakter.',
        ]);

        $report->update([
            'content' => $validated['content'] ?? null,
        ]);

        return redirect()
            ->route('petugas.reports.index')
            ->with('success', 'Draft laporan berhasil diperbarui.');
    }

    /**
     * Detail laporan untuk Admin atau Petugas yang ditugaskan.
     */
    public function show(Request $request, Report $report)
    {
        $user = $request->user();

        $isAdmin = $user->role === 'admin';

        abort_unless(
            $isAdmin || $user->role === 'petugas',
            403
        );

        $report->load([
            'activity.activityType',
            'submitter',
            'validator',
        ]);

        if (!$isAdmin) {
            $isAssigned = ActivityAssignment::query()
                ->where('activity_id', $report->activity_id)
                ->where('user_id', $user->id)
                ->exists();

            abort_unless($isAssigned, 403);
        }

        return view('reports.show', compact(
            'report',
            'isAdmin'
        ));
    }

    /**
     * Petugas mengirim draft atau laporan hasil revisi.
     */
    public function submit(Request $request, Report $report)
    {
        $user = $request->user();

        abort_unless($user->role === 'petugas', 403);

        DB::transaction(function () use ($report, $user) {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->findOrFail($report->id);

            $isAssigned = ActivityAssignment::query()
                ->where('activity_id', $lockedReport->activity_id)
                ->where('user_id', $user->id)
                ->exists();

            abort_unless($isAssigned, 403);

            if (
                !in_array(
                    $lockedReport->status,
                    ['draft', 'revision_needed'],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'status' =>
                        'Hanya draft atau laporan yang perlu direvisi yang dapat dikirim.',
                ]);
            }

            if (blank(trim((string) $lockedReport->content))) {
                throw ValidationException::withMessages([
                    'content' =>
                        'Isi laporan wajib diisi sebelum dikirim.',
                ]);
            }

            $lockedReport->update([
                'status' => 'submitted',
                'submitted_by' => $user->id,
                'submitted_at' => now(),

                // Review lama selesai ketika laporan dikirim ulang.
                'revision_note' => null,
                'validated_by' => null,
                'validated_at' => null,
            ]);
        });

        return redirect()
            ->route('petugas.reports.index')
            ->with('success', 'Laporan berhasil dikirim kepada Admin untuk divalidasi.');
    }

    /**
     * Admin menyetujui laporan yang telah dikirim.
     */
    public function validateReport(Request $request, Report $report)
    {
        $user = $request->user();

        abort_unless($user->role === 'admin', 403);

        DB::transaction(function () use ($report, $user) {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->findOrFail($report->id);

            if ($lockedReport->status !== 'submitted') {
                throw ValidationException::withMessages([
                    'status' =>
                        'Hanya laporan yang menunggu validasi yang dapat disetujui.',
                ]);
            }

            $lockedReport->update([
                'status' => 'validated',
                'validated_by' => $user->id,
                'validated_at' => now(),
                'revision_note' => null,
            ]);
        });

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Laporan berhasil divalidasi.');
    }

    /**
     * Admin meminta revisi dan memberikan catatan.
     */
    public function requestRevision(
        Request $request,
        Report $report
    ) {
        $user = $request->user();

        abort_unless($user->role === 'admin', 403);

        $validated = $request->validate([
            'revision_note' => [
                'required',
                'string',
                'min:3',
                'max:5000',
            ],
        ], [
            'revision_note.required' => 'Catatan revisi wajib diisi.',
            'revision_note.min' => 'Catatan revisi minimal 3 karakter.',
            'revision_note.max' => 'Catatan revisi maksimal 5000 karakter.',
        ]);

        DB::transaction(function () use ($report, $user, $validated) {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->findOrFail($report->id);

            if ($lockedReport->status !== 'submitted') {
                throw ValidationException::withMessages([
                    'status' =>
                        'Hanya laporan yang menunggu validasi yang dapat diminta revisi.',
                ]);
            }

            $lockedReport->update([
                'status' => 'revision_needed',
                'revision_note' => $validated['revision_note'],
                'validated_by' => $user->id,
                'validated_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Laporan dikembalikan kepada Petugas untuk direvisi.');
    }


}
