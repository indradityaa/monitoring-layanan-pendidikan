<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed data master
        $this->call([
            ActivityTypeSeeder::class,
            LocationSeeder::class,
        ]);

        // Buat user demo
        User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@demo.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Petugas Demo',
            'email' => 'petugas@demo.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        User::create([
            'name' => 'Pimpinan Demo',
            'email' => 'pimpinan@demo.test',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
        ]);

        // Seed data kegiatan
        $this->call([
            ActivitySeeder::class,
            ScheduleSeeder::class,
            ActivityAssignmentSeeder::class,
            ParticipantSeeder::class,
            ActivityParticipantSeeder::class,
            AttendanceSeeder::class,
            DocumentationSeeder::class,
            ReportSeeder::class,
        ]);
    }
}