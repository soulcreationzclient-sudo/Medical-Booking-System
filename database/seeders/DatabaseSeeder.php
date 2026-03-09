<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'super_admin',
            'email' => 'admin@gmail.com',
            'password'=>Hash::make('Nithila1234@'),
            'role'=>'super_admin',
            'status'=>1,
            'api_code'=>'nithii'
        ]);
        //  User::factory()->create([
        //     'name' => 'abi',
        //     'email' => 'yahi07@gmail.com',
        //     'password'=>Hash::make('Nithila1234@'),
        //     'role'=>'doctor',
        //     'status'=>1,
        //     'api_code'=>'abi'
        // ]);
    }
}
