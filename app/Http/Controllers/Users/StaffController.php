<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class StaffController extends Controller
{
  
    public function create()
    {
        return view('Settings.sections.create_staff');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:cashier,manager',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('settings.index')->with('success', 'User member created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('Settings.sections.edit_staff', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:cashier,manager',
        ]);

        $user->fill($request->only(['name', 'email', 'phone', 'role']));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('settings.index')->with('success', 'User member updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $authUser = auth()->user();
        if (!$authUser || $authUser->role !== 'admin' || $authUser->id === $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $user->delete();

        return redirect()->route('settings.index')->with('success', 'User member deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return redirect()->back()->with('success', 'User member status updated successfully.');
    }
}