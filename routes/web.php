<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityAssignmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PimpinanDashboardController;
use App\Http\Controllers\PetugasDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'petugas' => redirect()->route('petugas.dashboard'),
        'pimpinan' => redirect()->route('pimpinan.dashboard'),

        default => abort(403, 'Role tidak dikenali.'),
    };
})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->name('admin.dashboard');

    Route::get('/admin/kegiatan', [ActivityController::class, 'index'])
        ->name('admin.activities.index');

    Route::get('/admin/kegiatan/tambah', [ActivityController::class, 'create'])
        ->name('admin.activities.create');

    Route::post('/admin/kegiatan', [ActivityController::class, 'store'])
        ->name('admin.activities.store');

    Route::get('/admin/kegiatan/{activity}', [ActivityController::class, 'show'])
        ->name('admin.activities.show');

    Route::get('/admin/kegiatan/{activity}/edit', [ActivityController::class, 'edit'])
        ->name('admin.activities.edit');

    Route::put('/admin/kegiatan/{activity}', [ActivityController::class, 'update'])
        ->name('admin.activities.update');

    Route::get('/admin/jadwal', [ScheduleController::class, 'index'])
        ->name('admin.schedules.index');

    Route::get('/admin/jadwal/tambah', [ScheduleController::class, 'create'])
        ->name('admin.schedules.create');

    Route::post('/admin/jadwal', [ScheduleController::class, 'store'])
        ->name('admin.schedules.store');

    Route::get('/admin/jadwal/{schedule}/edit', [ScheduleController::class, 'edit'])
        ->name('admin.schedules.edit');

    Route::put('/admin/jadwal/{schedule}', [ScheduleController::class, 'update'])
        ->name('admin.schedules.update');

    Route::get('/admin/penugasan', [ActivityAssignmentController::class, 'index'])
        ->name('admin.assignments.index');

    Route::get('/admin/penugasan/tambah', [ActivityAssignmentController::class, 'create'])
        ->name('admin.assignments.create');

    Route::post('/admin/penugasan', [ActivityAssignmentController::class, 'store'])
        ->name('admin.assignments.store');

    Route::get(
        '/admin/penugasan/{assignment}/edit',
        [ActivityAssignmentController::class, 'edit']
    )->name('admin.assignments.edit');

    Route::put(
        '/admin/penugasan/{assignment}',
        [ActivityAssignmentController::class, 'update']
    )->name('admin.assignments.update');
    Route::get('/admin/laporan', [ReportController::class, 'index'])
        ->name('admin.reports.index');
    // Detail laporan untuk Admin
    Route::get('/admin/laporan/{report}', [ReportController::class, 'show'])
        ->name('admin.reports.show');

    // Setujui laporan
    Route::post('/admin/laporan/{report}/validasi', [ReportController::class, 'validateReport'])
        ->name('admin.reports.validate');

    // Minta revisi laporan
    Route::post('/admin/laporan/{report}/revisi', [ReportController::class, 'requestRevision'])
        ->name('admin.reports.revision');

    Route::get(
        '/admin/master-data/jenis-kegiatan',
        [ActivityTypeController::class, 'index']
    )->name('admin.master.activity-types.index');

    Route::get(
        '/admin/master-data/jenis-kegiatan/tambah',
        [ActivityTypeController::class, 'create']
    )->name('admin.master.activity-types.create');

    Route::post(
        '/admin/master-data/jenis-kegiatan',
        [ActivityTypeController::class, 'store']
    )->name('admin.master.activity-types.store');

    Route::get(
        '/admin/master-data/jenis-kegiatan/{activityType}/edit',
        [ActivityTypeController::class, 'edit']
    )->name('admin.master.activity-types.edit');

    Route::put(
        '/admin/master-data/jenis-kegiatan/{activityType}',
        [ActivityTypeController::class, 'update']
    )->name('admin.master.activity-types.update');

    Route::patch(
        '/admin/master-data/jenis-kegiatan/{activityType}/status',
        [ActivityTypeController::class, 'toggleActive']
    )->name('admin.master.activity-types.toggle-active');

    Route::get(
        '/admin/master-data/lokasi',
        [LocationController::class, 'index']
    )->name('admin.master.locations.index');

    Route::get(
        '/admin/master-data/lokasi/tambah',
        [LocationController::class, 'create']
    )->name('admin.master.locations.create');

    Route::post(
        '/admin/master-data/lokasi',
        [LocationController::class, 'store']
    )->name('admin.master.locations.store');

    Route::get(
        '/admin/master-data/lokasi/{location}/edit',
        [LocationController::class, 'edit']
    )->name('admin.master.locations.edit');

    Route::put(
        '/admin/master-data/lokasi/{location}',
        [LocationController::class, 'update']
    )->name('admin.master.locations.update');

    Route::patch(
        '/admin/master-data/lokasi/{location}/status',
        [LocationController::class, 'toggleActive']
    )->name('admin.master.locations.toggle-active');

});



/*
|--------------------------------------------------------------------------
| Petugas
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'role:petugas'])->group(function () {

    Route::get('/petugas/dashboard', PetugasDashboardController::class)
        ->name('petugas.dashboard');
    Route::get('/petugas/laporan', [ReportController::class, 'index'])
        ->name('petugas.reports.index');

    Route::get('/petugas/laporan/tambah', [ReportController::class, 'create'])
        ->name('petugas.reports.create');
    // Detail laporan Petugas
    Route::get('/petugas/laporan/{report}', [ReportController::class, 'show'])
        ->name('petugas.reports.show');

    Route::post('/petugas/laporan', [ReportController::class, 'store'])
        ->name('petugas.reports.store');

    Route::get('/petugas/laporan/{report}/edit', [ReportController::class, 'edit'])
        ->name('petugas.reports.edit');

    Route::put('/petugas/laporan/{report}', [ReportController::class, 'update'])
        ->name('petugas.reports.update');

    // Kirim draft atau laporan hasil revisi
    Route::post('/petugas/laporan/{report}/kirim', [ReportController::class, 'submit'])
        ->name('petugas.reports.submit');


});



/*
|--------------------------------------------------------------------------
| Pimpinan
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'role:pimpinan'])->group(function () {

    Route::get('/pimpinan/dashboard', PimpinanDashboardController::class)
        ->name('pimpinan.dashboard');

});



/*
|--------------------------------------------------------------------------
| Role Testing
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/test', function () {
        return 'Halaman Admin - Akses Berhasil';
    })->name('admin.test');

});

Route::middleware(['auth', 'role:petugas'])->group(function () {

    Route::get('/petugas/test', function () {
        return 'Halaman Petugas - Akses Berhasil';
    })->name('petugas.test');

});

Route::middleware(['auth', 'role:pimpinan'])->group(function () {

    Route::get('/pimpinan/test', function () {
        return 'Halaman Pimpinan - Akses Berhasil';
    })->name('pimpinan.test');

});