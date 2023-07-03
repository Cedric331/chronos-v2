<?php

namespace Database\Seeders;

use App\Models\Companie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Companie::create([
            'name' => 'Free Protelco',
            'description' => 'Entitée Free Protelco',
            'logo' => null,
            'is_active' => true,
        ]);
    }
}
