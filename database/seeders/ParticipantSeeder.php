<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $participants = [
            [
                'name' => 'Andi Saputra',
                'institution' => 'SMP Demo 1',
                'position' => 'Guru',
            ],
            [
                'name' => 'Budi Santoso',
                'institution' => 'SMP Demo 2',
                'position' => 'Guru',
            ],
            [
                'name' => 'Citra Lestari',
                'institution' => 'SMA Demo 1',
                'position' => 'Guru',
            ],
            [
                'name' => 'Dewi Anggraini',
                'institution' => 'SMK Demo 1',
                'position' => 'Guru',
            ],
            [
                'name' => 'Eko Prasetyo',
                'institution' => 'SMA Demo 2',
                'position' => 'Guru',
            ],
            [
                'name' => 'Fajar Nugroho',
                'institution' => 'SD Demo 1',
                'position' => 'Guru',
            ],
            [
                'name' => 'Gita Maharani',
                'institution' => 'SMP Demo 3',
                'position' => 'Guru',
            ],
            [
                'name' => 'Hendra Wijaya',
                'institution' => 'SMK Demo 2',
                'position' => 'Guru',
            ],
            [
                'name' => 'Intan Permata',
                'institution' => 'SMA Demo 3',
                'position' => 'Guru',
            ],
            [
                'name' => 'Joko Setiawan',
                'institution' => 'SD Demo 2',
                'position' => 'Guru',
            ],
        ];

        foreach ($participants as $participant) {
            Participant::firstOrCreate(
                [
                    'name' => $participant['name'],
                    'institution' => $participant['institution'],
                ],
                [
                    'position' => $participant['position'],
                ]
            );
        }
    }
}