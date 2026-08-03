<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showAccount(): View
    {
        return view('my-account');
    }

    /* ── Register ───────────────────────────────────────── */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        UserProfile::create(['user_id' => $user->id]);

        Auth::login($user);
        $request->session()->regenerate();

        $redirect = match ($user->role) {
            'employer' => route('employer.dashboard'),
            default    => route('candidate.dashboard'),
        };

        return redirect($redirect)
            ->with('success', 'Welcome to SmartWorker! Your account has been created.');
    }

    /* ── Login ──────────────────────────────────────────── */
    public function login(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $throttleKey = 'login:'.Str::lower($validated['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors(['email' => "Too many login attempts. Please try again in {$seconds} seconds."])
                ->onlyInput('email');
        }

        if (! Auth::attempt(
            ['email' => $validated['email'], 'password' => $validated['password']],
            $request->boolean('remember')
        )) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        }

        $redirect = match ($user->role) {
            'admin'    => route('admin.dashboard'),
            'employer' => route('employer.dashboard'),
            default    => route('candidate.dashboard'),
        };

        return redirect()->intended($redirect)
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /* ── Logout ─────────────────────────────────────────── */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }

    /* ── Forgot Password ────────────────────────────────── */
    public function showForgotPassword(): View
    {
        return view('my-account', ['formMode' => 'forgot']);
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        Password::sendResetLink($request->only('email'));

        return redirect()->route('password.request')
            ->with('success', 'If an account exists for that email address, a password reset link has been sent.');
    }

    public function showResetPassword(string $token): View
    {
        return view('my-account', ['formMode' => 'reset', 'token' => $token, 'resetEmail' => request('email')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('my-account')->with('success', 'Password reset! Please log in.')
            : back()->withErrors(['email' => __($status)]);
    }
}
