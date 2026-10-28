<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Kota Yogyakarta',
                'address' => 'Lokasi Demo Kota Yogyakarta',
                'district' => 'Kota Yogyakarta',
                'city' => 'Yogyakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Sleman',
                'address' => 'Lokasi Demo Sleman',
                'district' => 'Sleman',
                'city' => 'Sleman',
                'is_active' => true,
            ],
            [
                'name' => 'Bantul',
                'address' => 'Lokasi Demo Bantul',
                'district' => 'Bantul',
                'city' => 'Bantul',
                'is_active' => true,
            ],
            [
                'name' => 'Kulon Progo',
                'address' => 'Lokasi Demo Kulon Progo',
                'district' => 'Kulon Progo',
                'city' => 'Kulon Progo',
                'is_active' => true,
            ],
            [
                'name' => 'Gunungkidul',
                'address' => 'Lokasi Demo Gunungkidul',
                'district' => 'Gunungkidul',
                'city' => 'Gunungkidul',
                'is_active' => true,
            ],
        ];

        Location::insert($locations);
    }
}