<?php

namespace Database\Seeders;

use App\Models\Hub;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hub::create([
            'name' => 'Gujan',
        ]);

        Hub::create([
            'name' => 'Libourne',
        ]);

        Hub::create([
            'name' => 'Bruges',
        ]);

        Hub::create([
            'name' => 'Agen',
        ]);
    }
}
