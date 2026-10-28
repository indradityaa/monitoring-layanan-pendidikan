<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $activities = Activity::orderBy('id')
            ->take(4)
            ->get();

        $petugas = User::where('role', 'petugas')->first();
        $admin = User::where('role', 'admin')->first();

        foreach ($activities as $index => $activity) {

            if ($index === 0) {
                Report::create([
                    'activity_id' => $activity->id,
                    'status' => 'draft',
                    'content' => 'Laporan masih dalam tahap penyusunan.',
                ]);
            } elseif ($index === 1) {
                Report::create([
                    'activity_id' => $activity->id,
                    'status' => 'submitted',
                    'content' => 'Kegiatan telah selesai dilaksanakan dan laporan telah disusun.',
                    'submitted_by' => $petugas?->id,
                    'submitted_at' => now()->subDays(2),
                ]);
            } elseif ($index === 2) {
                Report::create([
                    'activity_id' => $activity->id,
                    'status' => 'revision_needed',
                    'content' => 'Laporan kegiatan perlu diperbaiki pada bagian hasil kegiatan.',
                    'submitted_by' => $petugas?->id,
                    'submitted_at' => now()->subDays(3),
                    'validated_by' => $admin?->id,
                    'revision_note' => 'Mohon melengkapi bagian hasil dan dokumentasi kegiatan.',
                ]);
            } elseif ($index === 3) {
                Report::create([
                    'activity_id' => $activity->id,
                    'status' => 'validated',
                    'content' => 'Kegiatan telah selesai dilaksanakan dan laporan telah diperiksa.',
                    'submitted_by' => $petugas?->id,
                    'submitted_at' => now()->subDays(5),
                    'validated_by' => $admin?->id,
                    'validated_at' => now()->subDays(4),
                ]);
            }
        }
    }
}