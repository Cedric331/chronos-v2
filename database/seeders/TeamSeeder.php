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
            'company_id' => 1,
        ]);

        Team::create([
            'name' => 'Libourne',
            'company_id' => 1,
        ]);

        Team::create([
            'name' => 'Bruges',
            'company_id' => 1,
        ]);

        Team::create([
            'name' => 'Agen',
            'company_id' => 1,
        ]);
    }
}
