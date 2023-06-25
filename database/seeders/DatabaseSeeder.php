<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);
        $this->call([
            TeamSeeder::class,
        ]);
        $users = User::factory(1)->create([
            'email' => "limacedric@hotmail.fr"
        ]);
        $user = $users->first();
        $users = User::factory(5)->create();

        $user->assignRole('Admin');

        Artisan::call('planning:generate');


        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
