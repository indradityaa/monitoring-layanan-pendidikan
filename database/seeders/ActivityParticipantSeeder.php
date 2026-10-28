<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\Participant;
use Illuminate\Database\Seeder;

class ActivityParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $activity1 = Activity::where('code', 'KGT-001')->firstOrFail();
        $activity2 = Activity::where('code', 'KGT-002')->firstOrFail();

        $participants = Participant::orderBy('id')->get();

        // Peserta Kegiatan 1
        foreach ($participants->take(5) as $participant) {
            ActivityParticipant::create([
                'activity_id' => $activity1->id,
                'participant_id' => $participant->id,
                'registered_at' => now(),
            ]);
        }

        // Peserta Kegiatan 2
        foreach ($participants->slice(4, 5) as $participant) {
            ActivityParticipant::create([
                'activity_id' => $activity2->id,
                'participant_id' => $participant->id,
                'registered_at' => now(),
            ]);
        }
    }
}