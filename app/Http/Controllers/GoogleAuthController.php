<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Team;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage(), ['exception' => $e]);
            
            $errorMessage = $e->getMessage();
            if (empty($errorMessage)) {
                if ($e instanceof \Laravel\Socialite\Two\InvalidStateException) {
                    $errorMessage = 'Sesi masuk Google Anda telah kedaluwarsa atau tidak valid (State Mismatch). Silakan coba bersihkan cookie/cache browser Anda dan klik tombol login kembali.';
                } else {
                    $errorMessage = 'Terjadi kesalahan koneksi atau proses login dibatalkan oleh pengguna. Pastikan server terhubung ke internet dan SSL terkonfigurasi dengan benar.';
                }
            }
            
            return redirect()->route('login')->withErrors([
                'error' => 'Gagal melakukan autentikasi dengan akun Google. Detail: ' . $errorMessage
            ]);
        }

        // Check if a user with this google_id or email already exists
        $existingUser = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($existingUser) {
            if ($existingUser->is_locked) {
                return redirect()->route('login')->withErrors([
                    'error' => 'Akun Anda telah dinonaktifkan atau ditutup. Silakan hubungi admin.'
                ]);
            }

            // Link Google ID if not set
            if (empty($existingUser->google_id)) {
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }

            // Sync avatar if empty
            if ($googleUser->getAvatar() && empty($existingUser->avatar)) {
                $existingUser->update([
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($existingUser);
            request()->session()->regenerate();
            session()->flash('login_success_popup', 'Selamat datang kembali, ' . $existingUser->name . '! Anda berhasil masuk menggunakan Google.');

            return $this->redirectUser();
        }

        // Store Google details in session for completing registration (adding Team Name)
        session(['google_user' => [
            'id' => $googleUser->getId(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
        ]]);

        return redirect()->route('register.google.complete');
    }

    /**
     * Show the form to complete registration for Google users.
     */
    public function showCompleteRegistration()
    {
        if (!session()->has('google_user')) {
            return redirect()->route('register');
        }

        $googleData = session('google_user');
        return view('auth.register-google', compact('googleData'));
    }

    /**
     * Process registration completion for Google users.
     */
    public function completeRegistration(Request $request)
    {
        if (!session()->has('google_user')) {
            return redirect()->route('register');
        }

        $googleData = session('google_user');

        $request->validate([
            'team_name' => ['required', 'string', 'max:255', 'unique:teams,name'],
        ], [
            'team_name.unique' => 'Nama tim futsal sudah terdaftar.',
        ]);

        DB::beginTransaction();
        try {
            // Create Team
            $team = Team::create([
                'name' => $request->team_name,
                'plan' => 'free',
            ]);

            // Find management role
            $managementRole = Role::where('name', 'management')->firstOrFail();

            // Create Manager User
            $user = User::create([
                'name' => $googleData['name'],
                'email' => $googleData['email'],
                'google_id' => $googleData['id'],
                'avatar' => $googleData['avatar'],
                'role_id' => $managementRole->id,
                'team_id' => $team->id,
                'password' => null, // Password can be null for Google users
            ]);

            DB::commit();

            // Clean up session
            session()->forget('google_user');

            Auth::login($user);
            $request->session()->regenerate();
            session()->flash('register_success_popup', 'Pendaftaran tim futsal "' . $team->name . '" menggunakan Google berhasil! Selamat bergabung.');

            return $this->redirectUser();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Gagal mendaftarkan akun. Silakan hubungi admin atau coba lagi.'
            ]);
        }
    }

    /**
     * Redirect the user according to their role.
     */
    protected function redirectUser()
    {
        $user = Auth::user();
        $slug = $user->isSuperAdmin() ? 'superadmin' : ($user->slug ?? 'user');
        
        $fallbackUrl = route($user->isSuperAdmin() ? 'superadmin.dashboard' : 'dashboard', ['slug' => $slug]);
        return redirect()->intended($fallbackUrl);
    }
}
