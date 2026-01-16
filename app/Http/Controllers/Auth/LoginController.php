<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        $email = Str::lower(trim($request->input('email')));
        $throttleKey = $email . '|' . $request->ip();

        // Simple rate limiting per email+IP to mitigate brute-force attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $identifier = hash('sha256', $email);
            $this->logActivity('Throttled Login Attempt', ['identifier' => $identifier]);
            return back()->withInput()->with('error', 'Too many login attempts. Please try again later.');
        }

        $user = User::where('email', $email)->first();

        // Use a dummy password hash to mitigate timing-based user enumeration.
        // This is Laravel's default bcrypt hash for the string "password".
        $dummyPasswordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

        $hashedPassword = $user ? $user->password : $dummyPasswordHash;
        $passwordValid = Hash::check($request->password, $hashedPassword);

        // Use an anonymized identifier in logs instead of raw email addresses.
        $identifier = hash('sha256', $email);
        $this->logActivity('Login Attempt', ['identifier' => $identifier]);

        if ($user && $passwordValid && $user->status === 'active') {
            $this->logActivity('Successful Login', [
                'user_id' => $user->id,
                'identifier' => $identifier,
            ]);

            Auth::login($user);
            // Prevent session fixation
            $request->session()->regenerate();

            // Clear any throttle counters on successful login
            RateLimiter::clear($throttleKey);

            if ($user->isAdmin()) {
                return redirect()->route('pos.dashboard');
            } else {
                return redirect()->route('pos.cart');
            }
        }

        // Increment throttle counter for failed attempts
        RateLimiter::hit($throttleKey, 60);

        if ($user && $passwordValid && $user->status !== 'active') {
            // Log an inactive account attempt without revealing details to the user
            $this->logActivity('Inactive Account Login Attempt', [
                'user_id' => $user->id,
                'identifier' => $identifier,
            ]);
        } else {
            $this->logActivity('Failed Login Attempt', [
                'identifier' => $identifier,
            ]);
        }

        // Generic error message for all authentication failures
        return back()->withInput()->with('error', 'Invalid email or password');
    }



public function logout(Request $request)
{
    if (auth()->check()) {
        $identifier = hash('sha256', Str::lower(trim(auth()->user()->email)));
        $this->logActivity('User Logout', [
            'user_id' => auth()->id(),
            'identifier' => $identifier,
        ]);
    }

    Auth::logout();

    // Invalidate session and regenerate CSRF token to prevent session fixation
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}

}