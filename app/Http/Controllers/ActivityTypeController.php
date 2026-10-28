<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityTypeController extends Controller
{
    /**
     * Daftar jenis kegiatan dan filter.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'active' => [
                'nullable',
                Rule::in(['1', '0']),
            ],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $activeFilter = $filters['active'] ?? '';

        $query = ActivityType::query()
            ->withCount('activities');

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($activeFilter !== '') {
            $query->where(
                'is_active',
                $activeFilter === '1'
            );
        }

        $activityTypes = $query
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('master.activity-types.index', compact(
            'activityTypes',
            'search',
            'activeFilter'
        ));
    }

    /**
     * Form tambah jenis kegiatan.
     */
    public function create()
    {
        return view('master.activity-types.create');
    }

    /**
     * Menyimpan jenis kegiatan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:activity_types,name',
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'name.required' => 'Nama jenis kegiatan wajib diisi.',
            'name.max' => 'Nama jenis kegiatan maksimal 100 karakter.',
            'name.unique' => 'Nama jenis kegiatan tersebut sudah tersedia.',
            'description.max' => 'Deskripsi maksimal 5000 karakter.',
        ]);

        $activityType = ActivityType::create([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.master.activity-types.index')
            ->with(
                'success',
                "Jenis kegiatan {$activityType->name} berhasil ditambahkan."
            );
    }

    /**
     * Form edit jenis kegiatan.
     */
    public function edit(ActivityType $activityType)
    {
        $activityType->loadCount('activities');

        return view('master.activity-types.edit', compact(
            'activityType'
        ));
    }

    /**
     * Memperbarui nama dan deskripsi.
     * Status aktif dikelola melalui aksi terpisah.
     */
    public function update(
        Request $request,
        ActivityType $activityType
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('activity_types', 'name')
                    ->ignore($activityType->id),
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'name.required' => 'Nama jenis kegiatan wajib diisi.',
            'name.max' => 'Nama jenis kegiatan maksimal 100 karakter.',
            'name.unique' => 'Nama jenis kegiatan tersebut sudah digunakan.',
            'description.max' => 'Deskripsi maksimal 5000 karakter.',
        ]);

        $activityType->update([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.master.activity-types.index')
            ->with('success', 'Jenis kegiatan berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan jenis kegiatan.
     */
    public function toggleActive(ActivityType $activityType)
    {
        $newStatus = !$activityType->is_active;

        $activityType->update([
            'is_active' => $newStatus,
        ]);

        $message = $newStatus
            ? 'Jenis kegiatan berhasil diaktifkan.'
            : 'Jenis kegiatan berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.master.activity-types.index')
            ->with('success', $message);
    }
}
