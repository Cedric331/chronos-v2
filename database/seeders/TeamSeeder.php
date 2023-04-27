<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::create([
            'name' => 'Gujan',
        ]);

        Team::create([
            'name' => 'Libourne',
        ]);

        Team::create([
            'name' => 'Bruges',
        ]);

        Team::create([
            'name' => 'Agen',
        ]);
    }
}
