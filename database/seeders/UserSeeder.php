<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Shamim',
            'mobile' => '01712960833',
            'email' => 's.shaimm.cse@gmail.com',
            'email_verified_at' => now(),
            'password' => '12345678',            
            'status' => 'Active',
        ]);

        User::create([
            'name' => 'Admin',
            'mobile' => '01712000001',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => '12345678',        
            'status' => 'Active',
        ]);
    }
}
