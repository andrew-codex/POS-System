<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        
        if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('adminadmin'), 
                'role' => 'admin',
            ]);

            $this->command->info('Admin user created: admin@gmail.com / adminadmin');
        } else {
            $this->command->info('Admin user already exists');
        }
    }
}