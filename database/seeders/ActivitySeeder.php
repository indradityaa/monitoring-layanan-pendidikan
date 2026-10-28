<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $petugas = User::where('role', 'petugas')->firstOrFail();

        $bimtek = ActivityType::where('name', 'Bimtek')->firstOrFail();
        $mls = ActivityType::where('name', 'MLS')->firstOrFail();

        Activity::create([
            'code' => 'KGT-001',
            'name' => 'Bimtek Pemanfaatan AI dalam Pembelajaran',
            'activity_type_id' => $bimtek->id,
            'description' => 'Kegiatan pelatihan pemanfaatan AI untuk mendukung pembelajaran.',
            'target_participants' => 30,
            'created_by' => $admin->id,
            'person_in_charge_id' => $petugas->id,
            'status' => 'planned',
        ]);

        Activity::create([
            'code' => 'KGT-002',
            'name' => 'Mobile Learning Services',
            'activity_type_id' => $mls->id,
            'description' => 'Kegiatan layanan pembelajaran berbasis teknologi.',
            'target_participants' => 25,
            'created_by' => $admin->id,
            'person_in_charge_id' => $petugas->id,
            'status' => 'scheduled',
        ]);
    }
}