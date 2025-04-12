<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //        $this->call([
        //            CompanieSeeder::class,
        //        ]);
        //        $this->call([
        //            RoleSeeder::class,
        //        ]);
        //        $this->call([
        //            TeamSeeder::class,
        //        ]);
        $this->call([
            TicketSystemSeeder::class,
        ]);

        //        Permission::create(['name' => 'access-admin']);

        //         \App\Models\User::factory()->create([
        //             'name' => 'Test User',
        //             'email' => 'test@example.com',
        //         ]);
    }
}
