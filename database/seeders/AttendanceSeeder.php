<?php

namespace Database\Seeders;

use App\Models\ActivityParticipant;
use App\Models\Attendance;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $activityParticipants = ActivityParticipant::orderBy('id')->get();

        foreach ($activityParticipants as $index => $activityParticipant) {
            $status = match ($index % 5) {
                0, 1, 2 => 'present',
                3 => 'permission',
                default => 'absent',
            };

            Attendance::create([
                'activity_participant_id' => $activityParticipant->id,
                'status' => $status,
                'attendance_time' => $status === 'present'
                    ? now()
                    : null,
                'note' => $status === 'permission'
                    ? 'Izin karena keperluan tertentu.'
                    : null,
            ]);
        }
    }
}