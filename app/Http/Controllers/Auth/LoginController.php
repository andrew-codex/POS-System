<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;
class LoginController extends Controller
{
    use LogsActivity;
    public function showLoginForm()
    {
        return view('auth.login');
    }

  public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

  
    $this->logActivity("Login Attempt", "Email: {$request->email}");

    if ($user && Hash::check($request->password, $user->password)) {

      
        if ($user->status !== 'active') {

            $this->logActivity("Inactive Account Login Attempt", [
                "email" => $user->email,
                "user_id" => $user->id
            ]);

            return back()->withInput()
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

      
        $this->logActivity("Successful Login", [
            "user_id" => $user->id,
            "email" => $user->email
        ]);

        Auth::login($user);

        if ($user->isAdmin()) {
            return redirect()->route('pos.dashboard');
        } else {
            return redirect()->route('pos.cart');
        }
    }

    
    $this->logActivity("Failed Login Attempt", [
        "email" => $request->email
    ]);

    return back()->withInput()
        ->with('error', 'Invalid email or password');
}



public function logout(Request $request)
{
    if (auth()->check()) {
        $this->logActivity("User Logout", [
            "user_id" => auth()->id(),
            "email" => auth()->user()->email
        ]);
    }

    Auth::logout();
    return redirect()->route('login');
}

}