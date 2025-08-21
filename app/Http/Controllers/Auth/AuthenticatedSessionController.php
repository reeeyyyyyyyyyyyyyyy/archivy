<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on user role
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . ($user->username ?? $user->name) . '! 🎉');
        } elseif ($user->isStaff()) {
            return redirect()->intended(route('staff.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . ($user->username ?? $user->name) . '! 🎉');
        } elseif ($user->isIntern()) {
            return redirect()->intended(route('intern.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . ($user->username ?? $user->name) . '! 🎉');
        }

        // Fallback to admin dashboard
        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
