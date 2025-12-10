<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CashierUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'cashier@gmail.com')->exists()) {
            User::create([
                'name' => 'Cashier',
                'email' => 'cashier@gmail.com',
                'password' => Hash::make('cashier123'), 
                'role' => 'cashier',
            ]);

            $this->command->info('Cashier user created: cashier@gmail.com / cashier123');
        } else {
            $this->command->info('Cashier user already exists.');
        }
    }
}
