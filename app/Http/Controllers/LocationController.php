<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    /**
     * Daftar lokasi beserta filter.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'active' => [
                'nullable',
                Rule::in(['1', '0']),
            ],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $activeFilter = $filters['active'] ?? '';

        $query = Location::query()
            ->withCount('schedules');

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($activeFilter !== '') {
            $query->where(
                'is_active',
                $activeFilter === '1'
            );
        }

        $locations = $query
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('master.locations.index', compact(
            'locations',
            'search',
            'activeFilter'
        ));
    }

    /**
     * Form tambah lokasi.
     */
    public function create()
    {
        return view('master.locations.create');
    }

    /**
     * Simpan lokasi baru.
     */
    public function store(Request $request)
    {
        // Rapikan spasi sebelum melakukan validasi.
        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:locations,name',
            ],
            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'district' => [
                'nullable',
                'string',
                'max:100',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
        ], [
            'name.required' => 'Nama lokasi wajib diisi.',
            'name.max' => 'Nama lokasi maksimal 150 karakter.',
            'name.unique' => 'Nama lokasi tersebut sudah terdaftar.',
            'address.max' => 'Alamat maksimal 5000 karakter.',
            'district.max' => 'Nama kecamatan maksimal 100 karakter.',
            'city.max' => 'Nama kota/kabupaten maksimal 100 karakter.',
        ]);

        $location = Location::create([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'district' => $validated['district'] ?? null,
            'city' => $validated['city'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.master.locations.index')
            ->with(
                'success',
                "Lokasi {$location->name} berhasil ditambahkan."
            );
    }

    /**
     * Form edit lokasi.
     */
    public function edit(Location $location)
    {
        $location->loadCount('schedules');

        return view('master.locations.edit', compact('location'));
    }

    /**
     * Perbarui informasi lokasi.
     */
    public function update(Request $request, Location $location)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('locations', 'name')
                    ->ignore($location->id),
            ],
            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'district' => [
                'nullable',
                'string',
                'max:100',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
        ], [
            'name.required' => 'Nama lokasi wajib diisi.',
            'name.max' => 'Nama lokasi maksimal 150 karakter.',
            'name.unique' => 'Nama lokasi tersebut sudah digunakan.',
            'address.max' => 'Alamat maksimal 5000 karakter.',
            'district.max' => 'Nama kecamatan maksimal 100 karakter.',
            'city.max' => 'Nama kota/kabupaten maksimal 100 karakter.',
        ]);

        $location->update([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'district' => $validated['district'] ?? null,
            'city' => $validated['city'] ?? null,
        ]);

        return redirect()
            ->route('admin.master.locations.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Aktifkan atau nonaktifkan lokasi.
     */
    public function toggleActive(Location $location)
    {
        $newStatus = !$location->is_active;

        $location->update([
            'is_active' => $newStatus,
        ]);

        $message = $newStatus
            ? 'Lokasi berhasil diaktifkan.'
            : 'Lokasi berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.master.locations.index')
            ->with('success', $message);
    }
}
