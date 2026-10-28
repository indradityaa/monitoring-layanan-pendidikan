<?php

use App\Models\Activity;
use App\Models\ActivityAssignment;
use App\Models\ActivityType;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();

    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->petugas = User::factory()->create([
        'role' => 'petugas',
    ]);

    $this->activityType = ActivityType::create([
        'name' => 'Jenis Uji CRUD',
        'description' => 'Data untuk pengujian otomatis',
        'is_active' => true,
    ]);
});

/**
 * Test CRUD kegiatan.
 */
test('admin dapat membuat dan memperbarui kegiatan', function () {
    $this->actingAs($this->admin);

    // Buat kegiatan.
    $this->post(
        route('admin.activities.store'),
        [
            'name' => 'Kegiatan Uji CRUD',
            'activity_type_id' => $this->activityType->id,
            'description' => 'Deskripsi awal',
            'target_participants' => 20,
            'person_in_charge_id' => $this->petugas->id,
            'status' => 'planned',
        ]
    );

    $activity = Activity::where(
        'name',
        'Kegiatan Uji CRUD'
    )->firstOrFail();

    $this->assertDatabaseHas('activities', [
        'id' => $activity->id,
        'name' => 'Kegiatan Uji CRUD',
        'activity_type_id' => $this->activityType->id,
        'created_by' => $this->admin->id,
        'status' => 'planned',
    ]);

    // Perbarui kegiatan yang sama.
    $this->put(
        route('admin.activities.update', $activity),
        [
            'name' => 'Kegiatan Uji CRUD Diperbarui',
            'activity_type_id' => $this->activityType->id,
            'description' => 'Deskripsi baru',
            'target_participants' => 25,
            'person_in_charge_id' => $this->petugas->id,
            'status' => 'planned',
        ]
    );

    $this->assertDatabaseHas('activities', [
        'id' => $activity->id,
        'name' => 'Kegiatan Uji CRUD Diperbarui',
        'description' => 'Deskripsi baru',
        'target_participants' => 25,
    ]);

    // Kegiatan diperbarui, bukan dibuat ulang.
    $this->assertDatabaseCount('activities', 1);
});

/**
 * Test pembuatan jadwal dengan lokasi berupa teks.
 */
test('admin dapat membuat jadwal dengan lokasi baru', function () {
    $activity = Activity::create([
        'code' => 'CRUD-001',
        'name' => 'Kegiatan Jadwal Uji',
        'activity_type_id' => $this->activityType->id,
        'description' => 'Pengujian jadwal',
        'target_participants' => 10,
        'created_by' => $this->admin->id,
        'person_in_charge_id' => $this->petugas->id,
        'status' => 'planned',
    ]);

    $scheduleDate = now()
        ->addDays(7)
        ->toDateString();

    $response = $this->actingAs($this->admin)->post(
        route('admin.schedules.store'),
        [
            'activity_id' => $activity->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'location_name' => 'Lokasi CRUD Test',
            'status' => 'scheduled',
            'notes' => 'Catatan pengujian',
        ]
    );

    $response->assertRedirect(route('admin.schedules.index'));

    $location = Location::where(
        'name',
        'Lokasi CRUD Test'
    )->firstOrFail();

    $this->assertDatabaseHas('schedules', [
        'activity_id' => $activity->id,
        'location_id' => $location->id,
        'status' => 'scheduled',
    ]);

    $savedSchedule = Schedule::where(
        'activity_id',
        $activity->id
    )->firstOrFail();

    expect($savedSchedule->schedule_date->format('Y-m-d'))
        ->toBe($scheduleDate);

    $this->assertDatabaseCount('locations', 1);
    $this->assertDatabaseCount('schedules', 1);
});

/**
 * Test penjadwalan ulang.
 */
test('perubahan tanggal dan waktu mengubah status jadwal menjadi rescheduled', function () {
    $activity = Activity::create([
        'code' => 'CRUD-002',
        'name' => 'Kegiatan Reschedule Uji',
        'activity_type_id' => $this->activityType->id,
        'created_by' => $this->admin->id,
        'person_in_charge_id' => $this->petugas->id,
        'status' => 'planned',
    ]);

    $location = Location::create([
        'name' => 'Lokasi Reschedule Uji',
        'address' => null,
        'district' => null,
        'city' => null,
        'is_active' => true,
    ]);

    $schedule = Schedule::create([
        'activity_id' => $activity->id,
        'schedule_date' => now()->addDays(5)->toDateString(),
        'start_time' => '08:00',
        'end_time' => '12:00',
        'location_id' => $location->id,
        'status' => 'scheduled',
        'notes' => null,
    ]);

    $newDate = now()
        ->addDays(10)
        ->toDateString();

    $response = $this->actingAs($this->admin)->put(
        route('admin.schedules.update', $schedule),
        [
            'schedule_date' => $newDate,
            'start_time' => '09:00',
            'end_time' => '13:00',
            'location_name' => 'Lokasi Reschedule Uji',
            'status' => 'scheduled',
            'notes' => 'Jadwal diubah untuk pengujian',
        ]
    );

    $response->assertRedirect(route('admin.schedules.index'));

    $this->assertDatabaseHas('schedules', [
        'id' => $schedule->id,
        'status' => 'rescheduled',
    ]);

    $savedSchedule = Schedule::findOrFail($schedule->id);

    expect($savedSchedule->schedule_date->format('Y-m-d'))
        ->toBe($newDate)
        ->and(substr($savedSchedule->start_time, 0, 5))
        ->toBe('09:00')
        ->and(substr($savedSchedule->end_time, 0, 5))
        ->toBe('13:00');

    // Status kegiatan induk tidak berubah otomatis.
    $this->assertDatabaseHas('activities', [
        'id' => $activity->id,
        'status' => 'planned',
    ]);

    $this->assertDatabaseCount('schedules', 1);
});

/**
 * Test penugasan petugas dan pencegahan duplikasi.
 */
test('penugasan petugas yang sama tidak dibuat dua kali', function () {
    $activity = Activity::create([
        'code' => 'CRUD-003',
        'name' => 'Kegiatan Penugasan Uji',
        'activity_type_id' => $this->activityType->id,
        'created_by' => $this->admin->id,
        'person_in_charge_id' => $this->petugas->id,
        'status' => 'planned',
    ]);

    $this->actingAs($this->admin);

    $payload = [
        'activity_id' => $activity->id,
        'user_ids' => [$this->petugas->id],
        'assignment_roles' => [
            $this->petugas->id => 'Pendamping',
        ],
    ];

    // Simpan penugasan pertama.
    $this->post(
        route('admin.assignments.store'),
        $payload
    )->assertRedirect(route('admin.assignments.index'));

    $this->assertDatabaseHas('activity_assignments', [
        'activity_id' => $activity->id,
        'user_id' => $this->petugas->id,
        'assignment_role' => 'Pendamping',
    ]);

    // Kirim ulang pasangan kegiatan dan petugas yang sama.
    $payload['assignment_roles'][$this->petugas->id] = 'Narasumber';

    $this->post(
        route('admin.assignments.store'),
        $payload
    )->assertRedirect(route('admin.assignments.index'));

    // Jumlah record tetap satu; peran diperbarui.
    $this->assertDatabaseCount('activity_assignments', 1);

    $this->assertDatabaseHas('activity_assignments', [
        'activity_id' => $activity->id,
        'user_id' => $this->petugas->id,
        'assignment_role' => 'Narasumber',
    ]);
});
