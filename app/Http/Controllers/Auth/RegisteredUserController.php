<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    public function store(RegisterRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
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
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang! Akun Anda berhasil dibuat dan sudah masuk ke sistem.');
            } elseif ($user->hasRole('staff')) {
                return redirect()->route('staff.dashboard')
                    ->with('success', 'Selamat datang! Akun Anda berhasil dibuat dan sudah masuk ke sistem.');
            } else {
                // Default to intern dashboard
                return redirect()->route('intern.dashboard')
                    ->with('success', 'Selamat datang! Akun Anda berhasil dibuat dan sudah masuk ke sistem.');
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
