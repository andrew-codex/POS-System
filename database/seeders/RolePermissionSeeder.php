<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesPermissions = config('permissions.roles');

        foreach ($rolesPermissions as $role => $permissions) {
            foreach ($permissions as $permission) {
             
                RolePermission::firstOrCreate([
                    'role' => $role,
                    'permission' => $permission,
                ]);
            }
        }

        $this->command->info('Role permissions seeded successfully!');
    }
}
