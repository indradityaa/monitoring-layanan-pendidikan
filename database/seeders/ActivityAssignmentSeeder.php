<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = User::where('role', 'petugas')->firstOrFail();

        $activities = Activity::all();

        foreach ($activities as $activity) {
            ActivityAssignment::create([
                'activity_id' => $activity->id,
                'user_id' => $petugas->id,
                'assignment_role' => 'Petugas',
            ]);
        }
    }
}