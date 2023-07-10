<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        Role::create([
            'name' => 'Responsable',
            'guard_name' => 'web',
        ]);
        Role::create([
            'name' => 'Coordinateur',
            'guard_name' => 'web',
        ]);
        Role::create([
            'name' => 'Conseiller',
            'guard_name' => 'web',
        ]);
    }
}
