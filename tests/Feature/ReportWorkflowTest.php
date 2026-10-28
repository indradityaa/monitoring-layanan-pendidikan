<?php

use App\Models\Activity;
use App\Models\ActivityAssignment;
use App\Models\ActivityType;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Membuat Admin dan Petugas khusus untuk pengujian.
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->petugas = User::factory()->create([
        'role' => 'petugas',
    ]);

    // Membuat jenis kegiatan dan kegiatan pengujian.
    $this->activityType = ActivityType::create([
        'name' => 'Jenis Uji Laporan',
        'description' => 'Data untuk automated test',
        'is_active' => true,
    ]);

    $this->activity = Activity::create([
        'code' => 'RPT-TEST-001',
        'name' => 'Kegiatan Uji Laporan',
        'activity_type_id' => $this->activityType->id,
        'description' => 'Kegiatan untuk pengujian alur laporan',
        'target_participants' => 10,
        'created_by' => $this->admin->id,
        'person_in_charge_id' => null,
        'status' => 'ongoing',
    ]);

    // Menetapkan Petugas pada kegiatan.
    ActivityAssignment::create([
        'activity_id' => $this->activity->id,
        'user_id' => $this->petugas->id,
        'assignment_role' => 'Pendamping',
    ]);

    // Membuat satu laporan berstatus Draft.
    $this->report = Report::create([
        'activity_id' => $this->activity->id,
        'status' => 'draft',
        'content' => 'Isi laporan untuk pengujian sistem.',
    ]);
});

test('petugas dapat mengirim laporan dan admin dapat memvalidasinya', function () {
    // Petugas mengirim laporan.
    $this->actingAs($this->petugas)
        ->post(route('petugas.reports.submit', $this->report))
        ->assertRedirect(route('petugas.reports.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'submitted',
        'submitted_by' => $this->petugas->id,
    ]);

    $this->report->refresh();

    expect($this->report->submitted_at)->not->toBeNull();

    // Admin memvalidasi laporan yang sudah dikirim.
    $this->actingAs($this->admin)
        ->post(route('admin.reports.validate', $this->report))
        ->assertRedirect(route('admin.reports.show', $this->report))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'validated',
        'validated_by' => $this->admin->id,
    ]);

    $this->report->refresh();

    expect($this->report->validated_at)->not->toBeNull();
});

test('petugas tidak dapat melihat laporan kegiatan yang tidak ditugaskan kepadanya', function () {
    $petugasLain = User::factory()->create([
        'role' => 'petugas',
    ]);

    $this->actingAs($petugasLain)
        ->get(route('petugas.reports.show', $this->report))
        ->assertForbidden();
});

test('laporan tanpa isi tidak dapat dikirim', function () {
    $this->report->update([
        'content' => '',
    ]);

    $this->actingAs($this->petugas)
        ->from(route('petugas.reports.show', $this->report))
        ->post(route('petugas.reports.submit', $this->report))
        ->assertSessionHasErrors('content');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'draft',
    ]);
});

test('admin tidak dapat memvalidasi laporan yang masih berstatus draft', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.reports.show', $this->report))
        ->post(route('admin.reports.validate', $this->report))
        ->assertSessionHasErrors('status');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'draft',
        'validated_by' => null,
        'validated_at' => null,
    ]);
});


test('laporan dapat direvisi dikirim ulang dan divalidasi kembali', function () {

    /*
     * Laporan harus dikirim terlebih dahulu agar berstatus submitted.
     * Admin hanya dapat meminta revisi pada laporan yang menunggu validasi.
     */
    $this->actingAs($this->petugas)
        ->post(route('petugas.reports.submit', $this->report))
        ->assertRedirect(route('petugas.reports.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'submitted',
        'submitted_by' => $this->petugas->id,
    ]);
    // 1. Admin meminta revisi.
    $this->actingAs($this->admin)
        ->post(
            route('admin.reports.revision', $this->report),
            [
                'revision_note' =>
                    'Lengkapi bagian hasil kegiatan dan tindak lanjut.',
            ]
        )
        ->assertRedirect(
            route('admin.reports.show', $this->report)
        )
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'revision_needed',
        'validated_by' => $this->admin->id,
        'revision_note' =>
            'Lengkapi bagian hasil kegiatan dan tindak lanjut.',
    ]);

    // 2. Petugas memperbarui isi laporan.
    $this->actingAs($this->petugas)
        ->put(
            route('petugas.reports.update', $this->report),
            [
                'content' =>
                    'Hasil kegiatan telah dilengkapi. '
                    . 'Tindak lanjut sudah dicantumkan.',
            ]
        )
        ->assertRedirect(route('petugas.reports.index'));

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'revision_needed',
        'content' =>
            'Hasil kegiatan telah dilengkapi. '
            . 'Tindak lanjut sudah dicantumkan.',
    ]);

    // 3. Petugas mengirim ulang laporan.
    $this->post(
        route('petugas.reports.submit', $this->report)
    )
        ->assertRedirect(route('petugas.reports.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'submitted',
        'revision_note' => null,
        'validated_by' => null,
        'validated_at' => null,
        'submitted_by' => $this->petugas->id,
    ]);

    $this->report->refresh();

    expect($this->report->submitted_at)->not->toBeNull();

    // 4. Admin menyetujui laporan hasil revisi.
    $this->actingAs($this->admin)
        ->post(
            route('admin.reports.validate', $this->report)
        )
        ->assertRedirect(
            route('admin.reports.show', $this->report)
        )
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reports', [
        'id' => $this->report->id,
        'status' => 'validated',
        'validated_by' => $this->admin->id,
        'revision_note' => null,
    ]);

    $this->report->refresh();

    expect($this->report->validated_at)->not->toBeNull();

    // Pastikan tetap satu laporan untuk kegiatan ini.
    $this->assertDatabaseCount('reports', 1);
});
