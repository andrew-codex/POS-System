<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RolePermission;
use App\Models\Setting;
use App\Models\Stock_logs;
use App\Traits\LogsActivity;
class SettingsController extends Controller
{
    use LogsActivity;
    public function index()
    {
       
        $roles = RolePermission::pluck('role')
            ->map(fn($r) => strtolower(trim($r)))
            ->unique()
            ->values()
            ->toArray();

       
        $allPermissions = RolePermission::pluck('permission')
            ->map(fn($p) => strtolower(trim($p)))
            ->unique()
            ->values()
            ->toArray();

       
        $rolePermissions = RolePermission::all()
            ->groupBy(fn($row) => strtolower(trim($row->role)))
            ->map(fn($group) => 
                $group->pluck('permission')
                      ->map(fn($p) => strtolower(trim($p)))
                      ->toArray()
            )
            ->toArray();

     
        $users = User::where('role', '!=', 'admin')->get();
        $auditLogs = Stock_logs::orderBy('created_at', 'desc')->paginate(10);

      $settings = Setting::pluck('value', 'key')->toArray();
        return view('Settings.settings', [
            'users' => $users,
            'roles' => $roles,
            'allPermissions' => $allPermissions,
            'rolePermissionsMap' => $rolePermissions,
            'settings' => $settings,
            'auditLogs' => $auditLogs,
        ]);
    }

    public function updateRolePermissions(Request $request, $role)
    {
        $permissions = $request->input('permissions', []);

    
        $role = strtolower(trim($role));
        $permissions = array_map(fn($p) => strtolower(trim($p)), $permissions);


        RolePermission::whereRaw('LOWER(TRIM(role)) = ?', [$role])->delete();

        
        foreach ($permissions as $permission) {
            RolePermission::create([
                'role' => $role,
                'permission' => $permission,
            ]);
        }

        $this->logActivity("Updated Role Permissions", [
            "role" => $role,
            "permissions" => $permissions,
        ]);

        return redirect()->back()->with('success', "Permissions updated for role: $role");
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'system_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        
        Setting::updateOrCreate(
            ['key' => 'system_name'],
            ['value' => $request->system_name]
        );

      
        if ($request->hasFile('system_logo')) {
            $path = $request->file('system_logo')->store('uploads', 'public');

            Setting::updateOrCreate(
                ['key' => 'system_logo'],
                ['value' => 'storage/' . $path]
            );
        }

        $this->logActivity("Updated System Settings", [
            "system_name" => $request->system_name,
            "system_logo" => $request->hasFile('system_logo') ? 'Updated' : 'Unchanged',
        ]);

        return back()->with('success', 'Settings updated successfully!');
    }



}
