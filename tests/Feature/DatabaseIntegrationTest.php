<?php

use App\Models\Activity;
use App\Models\ActivityAssignment;
use App\Models\ActivityType;
use App\Models\Location;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('jadwal terhubung dengan kegiatan dan lokasi yang benar', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $type = ActivityType::create([
        'name' => 'Bimtek',
        'description' => 'Data uji',
        'is_active' => true,
    ]);

    $location = Location::create([
        'name' => 'Lokasi Pengujian',
        'address' => 'Alamat pengujian',
        'district' => 'Sewon',
        'city' => 'Bantul',
        'is_active' => true,
    ]);

    $activity = Activity::create([
        'code' => 'TEST-001',
        'name' => 'Kegiatan Pengujian',
        'activity_type_id' => $type->id,
        'description' => 'Kegiatan untuk pengujian integrasi',
        'target_participants' => 20,
        'created_by' => $admin->id,
        'person_in_charge_id' => null,
        'status' => 'planned',
    ]);

    $schedule = Schedule::create([
        'activity_id' => $activity->id,
        'schedule_date' => now()->addDays(5)->toDateString(),
        'start_time' => '08:00',
        'end_time' => '12:00',
        'location_id' => $location->id,
        'status' => 'scheduled',
        'notes' => 'Jadwal pengujian',
    ]);

    expect($schedule->activity->id)->toBe($activity->id)
        ->and($schedule->location->id)->toBe($location->id);

    $this->assertDatabaseHas('schedules', [
        'id' => $schedule->id,
        'activity_id' => $activity->id,
        'location_id' => $location->id,
    ]);
});

test('database menolak penugasan duplikat untuk kegiatan dan petugas yang sama', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $petugas = User::factory()->create([
        'role' => 'petugas',
    ]);

    $type = ActivityType::create([
        'name' => 'Workshop',
        'description' => null,
        'is_active' => true,
    ]);

    $activity = Activity::create([
        'code' => 'TEST-002',
        'name' => 'Kegiatan Penugasan Uji',
        'activity_type_id' => $type->id,
        'created_by' => $admin->id,
        'person_in_charge_id' => null,
        'status' => 'planned',
    ]);

    ActivityAssignment::create([
        'activity_id' => $activity->id,
        'user_id' => $petugas->id,
        'assignment_role' => 'Pendamping',
    ]);

    $this->expectException(QueryException::class);

    ActivityAssignment::create([
        'activity_id' => $activity->id,
        'user_id' => $petugas->id,
        'assignment_role' => 'Narasumber',
    ]);
});

test('database membatasi satu laporan untuk setiap kegiatan', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $type = ActivityType::create([
        'name' => 'MLS',
        'description' => null,
        'is_active' => true,
    ]);

    $activity = Activity::create([
        'code' => 'TEST-003',
        'name' => 'Kegiatan Laporan Uji',
        'activity_type_id' => $type->id,
        'created_by' => $admin->id,
        'person_in_charge_id' => null,
        'status' => 'ongoing',
    ]);

    Report::create([
        'activity_id' => $activity->id,
        'status' => 'draft',
        'content' => 'Laporan pengujian pertama',
    ]);

    $this->expectException(QueryException::class);

    Report::create([
        'activity_id' => $activity->id,
        'status' => 'draft',
        'content' => 'Laporan duplikat',
    ]);
});

test('pembuatan jadwal menggunakan lokasi aktif tanpa membedakan kapitalisasi', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $type = ActivityType::create([
        'name' => 'Bimtek',
        'description' => null,
        'is_active' => true,
    ]);

    $location = Location::create([
        'name' => 'Sleman',
        'is_active' => true,
    ]);

    $activity = Activity::create([
        'code' => 'TEST-004',
        'name' => 'Kegiatan Lokasi Uji',
        'activity_type_id' => $type->id,
        'created_by' => $admin->id,
        'person_in_charge_id' => null,
        'status' => 'planned',
    ]);

    $response = $this->actingAs($admin)->post(
        route('admin.schedules.store'),
        [
            'activity_id' => $activity->id,
            'schedule_date' => now()->addDays(7)->toDateString(),
            'start_time' => '08:00',
            'end_time' => '12:00',
            'location_name' => 'SLEMAN',
            'status' => 'scheduled',
            'notes' => 'Tes pencocokan lokasi',
        ]
    );

    $response->assertRedirect(route('admin.schedules.index'));

    $this->assertDatabaseCount('locations', 1);

    $this->assertDatabaseHas('schedules', [
        'activity_id' => $activity->id,
        'location_id' => $location->id,
    ]);
});

test('jadwal baru ditolak jika nama lokasi hanya ditemukan dalam data nonaktif', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $type = ActivityType::create([
        'name' => 'Bimtek',
        'description' => null,
        'is_active' => true,
    ]);

    Location::create([
        'name' => 'Sleman',
        'is_active' => false,
    ]);

    $activity = Activity::create([
        'code' => 'TEST-005',
        'name' => 'Kegiatan Lokasi Nonaktif Uji',
        'activity_type_id' => $type->id,
        'created_by' => $admin->id,
        'person_in_charge_id' => null,
        'status' => 'planned',
    ]);

    $response = $this->actingAs($admin)->post(
        route('admin.schedules.store'),
        [
            'activity_id' => $activity->id,
            'schedule_date' => now()->addDays(7)->toDateString(),
            'start_time' => '08:00',
            'end_time' => '12:00',
            'location_name' => 'SLEMAN',
            'status' => 'scheduled',
            'notes' => null,
        ]
    );

    $response->assertSessionHasErrors('location_name');

    $this->assertDatabaseCount('locations', 1);
    $this->assertDatabaseCount('schedules', 0);
});
