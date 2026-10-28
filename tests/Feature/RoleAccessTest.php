<?php

use App\Models\User;

test('guest diarahkan ke halaman login saat membuka halaman terlindungi', function () {
    $this->get('/admin/kegiatan')
        ->assertRedirect(route('login'));

    $this->get('/petugas/laporan')
        ->assertRedirect(route('login'));

    $this->get('/pimpinan/dashboard')
        ->assertRedirect(route('login'));
});

test('petugas tidak dapat mengakses halaman admin', function () {
    $petugas = (new User())->forceFill([
        'id' => 910001,
        'name' => 'Petugas Test',
        'email' => 'petugas-test@example.test',
        'role' => 'petugas',
    ]);

    $this->actingAs($petugas)
        ->get('/admin/kegiatan')
        ->assertForbidden();

    $this->get('/admin/jadwal')
        ->assertForbidden();

    $this->get('/admin/master-data/lokasi')
        ->assertForbidden();
});

test('admin tidak dapat mengakses halaman khusus petugas', function () {
    $admin = (new User())->forceFill([
        'id' => 910002,
        'name' => 'Admin Test',
        'email' => 'admin-test@example.test',
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get('/petugas/laporan')
        ->assertForbidden();

    $this->get('/petugas/laporan/tambah')
        ->assertForbidden();
});

test('pimpinan tidak dapat mengakses halaman admin dan petugas', function () {
    $pimpinan = (new User())->forceFill([
        'id' => 910003,
        'name' => 'Pimpinan Test',
        'email' => 'pimpinan-test@example.test',
        'role' => 'pimpinan',
    ]);

    $this->actingAs($pimpinan)
        ->get('/admin/laporan')
        ->assertForbidden();

    $this->get('/admin/master-data/lokasi')
        ->assertForbidden();

    $this->get('/petugas/laporan')
        ->assertForbidden();
});

test('setiap role dapat mengakses route pengujian miliknya sendiri', function () {
    $roles = [
        'admin' => '/admin/test',
        'petugas' => '/petugas/test',
        'pimpinan' => '/pimpinan/test',
    ];

    foreach ($roles as $role => $url) {
        $user = (new User())->forceFill([
            'id' => match ($role) {
                'admin' => 910004,
                'petugas' => 910005,
                'pimpinan' => 910006,
            },
            'name' => ucfirst($role) . ' Test',
            'email' => $role . '-route-test@example.test',
            'role' => $role,
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertOk();
    }
});