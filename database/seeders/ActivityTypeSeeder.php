<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $activityTypes = [
            [
                'name' => 'MLS',
                'description' => 'Mobile Learning Services',
                'is_active' => true,
            ],
            [
                'name' => 'Bimtek',
                'description' => 'Bimbingan Teknis',
                'is_active' => true,
            ],
            [
                'name' => 'Workshop',
                'description' => 'Kegiatan workshop',
                'is_active' => true,
            ],
            [
                'name' => 'Pelatihan',
                'description' => 'Kegiatan pelatihan',
                'is_active' => true,
            ],
            [
                'name' => 'Pendampingan',
                'description' => 'Kegiatan pendampingan',
                'is_active' => true,
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Jenis kegiatan lainnya',
                'is_active' => true,
            ],
        ];

        ActivityType::insert($activityTypes);
    }
}