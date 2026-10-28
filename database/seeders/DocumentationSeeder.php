<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Documentation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $activity = Activity::where('code', 'KGT-001')->firstOrFail();

        $user = User::where('role', 'petugas')->firstOrFail();

        Documentation::create([
            'activity_id' => $activity->id,
            'file_name' => 'foto-kegiatan-demo-01.jpg',
            'file_path' => 'documentations/1/foto-kegiatan-demo-01.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 350000,
            'uploaded_by' => $user->id,
        ]);

        Documentation::create([
            'activity_id' => $activity->id,
            'file_name' => 'laporan-pendukung-demo.pdf',
            'file_path' => 'documentations/1/laporan-pendukung-demo.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 520000,
            'uploaded_by' => $user->id,
        ]);
    }
}