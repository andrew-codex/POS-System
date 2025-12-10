<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'system_name'],
            ['value' => 'POS System']
        );

        Setting::updateOrCreate(
            ['key' => 'system_logo'],
            ['value' => 'images/default-logo.jpg'] // your fallback
        );
    }
}
