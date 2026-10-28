<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Location;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $activity1 = Activity::where('code', 'KGT-001')->firstOrFail();
        $activity2 = Activity::where('code', 'KGT-002')->firstOrFail();

        $location1 = Location::where('name', 'Sleman')->firstOrFail();
        $location2 = Location::where('name', 'Bantul')->firstOrFail();

        Schedule::create([
            'activity_id' => $activity1->id,
            'schedule_date' => '2026-10-15',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'location_id' => $location1->id,
            'status' => 'scheduled',
            'notes' => 'Jadwal demo untuk testing sistem.',
        ]);

        Schedule::create([
            'activity_id' => $activity2->id,
            'schedule_date' => '2026-10-20',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'location_id' => $location2->id,
            'status' => 'scheduled',
            'notes' => 'Jadwal demo untuk testing sistem.',
        ]);
    }
}