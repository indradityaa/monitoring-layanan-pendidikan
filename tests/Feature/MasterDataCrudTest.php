<?php

use App\Models\ActivityType;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();

    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);
});

test('admin dapat membuka halaman daftar dan tambah master data', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.master.activity-types.index'))
        ->assertOk();

    $this->get(route('admin.master.activity-types.create'))
        ->assertOk();

    $this->get(route('admin.master.locations.index'))
        ->assertOk();

    $this->get(route('admin.master.locations.create'))
        ->assertOk();
});

test('admin dapat menambah mengedit dan menonaktifkan jenis kegiatan', function () {
    $this->actingAs($this->admin);

    // Tambah jenis kegiatan.
    $this->post(
        route('admin.master.activity-types.store'),
        [
            'name' => 'Workshop Pengujian',
            'description' => 'Jenis kegiatan untuk automated test.',
        ]
    )
        ->assertRedirect(route('admin.master.activity-types.index'))
        ->assertSessionHas('success');

    $type = ActivityType::where(
        'name',
        'Workshop Pengujian'
    )->firstOrFail();

    $this->assertDatabaseHas('activity_types', [
        'id' => $type->id,
        'name' => 'Workshop Pengujian',
        'is_active' => 1,
    ]);

    // Edit jenis kegiatan.
    $this->put(
        route('admin.master.activity-types.update', $type),
        [
            'name' => 'Workshop Pengujian Revisi',
            'description' => 'Deskripsi yang sudah diperbarui.',
        ]
    )
        ->assertRedirect(route('admin.master.activity-types.index'));

    $this->assertDatabaseHas('activity_types', [
        'id' => $type->id,
        'name' => 'Workshop Pengujian Revisi',
        'description' => 'Deskripsi yang sudah diperbarui.',
    ]);

    // Nonaktifkan jenis kegiatan.
    $this->patch(
        route('admin.master.activity-types.toggle-active', $type)
    )
        ->assertRedirect(route('admin.master.activity-types.index'));

    $this->assertDatabaseHas('activity_types', [
        'id' => $type->id,
        'is_active' => 0,
    ]);
});

test('admin dapat menambah mengedit dan menonaktifkan lokasi', function () {
    $this->actingAs($this->admin);

    // Tambah lokasi.
    $this->post(
        route('admin.master.locations.store'),
        [
            'name' => 'Lokasi Pengujian',
            'address' => 'Jalan Pengujian Nomor 1',
            'district' => 'Sewon',
            'city' => 'Bantul',
        ]
    )
        ->assertRedirect(route('admin.master.locations.index'))
        ->assertSessionHas('success');

    $location = Location::where(
        'name',
        'Lokasi Pengujian'
    )->firstOrFail();

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'name' => 'Lokasi Pengujian',
        'is_active' => 1,
    ]);

    // Edit lokasi tanpa mengganti record.
    $this->put(
        route('admin.master.locations.update', $location),
        [
            'name' => 'Lokasi Pengujian Revisi',
            'address' => 'Jalan Baru Nomor 2',
            'district' => 'Kasihan',
            'city' => 'Bantul',
        ]
    )
        ->assertRedirect(route('admin.master.locations.index'));

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'name' => 'Lokasi Pengujian Revisi',
        'address' => 'Jalan Baru Nomor 2',
        'district' => 'Kasihan',
    ]);

    // Nonaktifkan lokasi.
    $this->patch(
        route('admin.master.locations.toggle-active', $location)
    )
        ->assertRedirect(route('admin.master.locations.index'));

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'is_active' => 0,
    ]);
});

test('nama jenis kegiatan dan lokasi wajib diisi', function () {
    $this->actingAs($this->admin);

    $this->post(
        route('admin.master.activity-types.store'),
        [
            'name' => '',
            'description' => null,
        ]
    )
        ->assertSessionHasErrors('name');

    $this->post(
        route('admin.master.locations.store'),
        [
            'name' => '',
            'address' => null,
            'district' => null,
            'city' => null,
        ]
    )
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('activity_types', 0);
    $this->assertDatabaseCount('locations', 0);
});
