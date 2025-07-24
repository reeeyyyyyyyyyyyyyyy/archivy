<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto verify for convenience
            ]);

            // Assign default role 'intern' to new users
            $internRole = Role::where('name', 'intern')->first();
            if ($internRole) {
                $user->assignRole($internRole);
                Log::info("New user registered: {$user->name} ({$user->email}) with default role 'intern'");
            } else {
                Log::warning("Intern role not found, user registered without role: {$user->name} ({$user->email})");
            }

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            // Redirect based on role (default to intern dashboard)
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('staff')) {
                return redirect()->route('staff.dashboard');
            } else {
                // Default to intern dashboard
                return redirect()->route('intern.dashboard');
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User registration error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.']);
        }
    }
}
