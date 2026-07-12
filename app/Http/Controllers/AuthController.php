<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if ($user->is_locked) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan atau ditutup. Silakan hubungi admin.',
                ])->onlyInput('email');
            }
            $request->session()->regenerate();
            session()->flash('login_success_popup', 'Selamat datang kembali, ' . $user->name . '! Anda berhasil masuk ke platform.');
            return $this->redirectUser();
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'team_name' => ['required', 'string', 'max:255', 'unique:teams,name'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'team_name.unique' => 'Nama tim futsal sudah terdaftar.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
        ]);

        DB::beginTransaction();
        try {
            // Create the Team
            $team = Team::create([
                'name' => $request->team_name,
                'plan' => 'free',
            ]);

            // Find management role
            $managementRole = Role::where('name', 'management')->firstOrFail();

            // Create the Manager User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $managementRole->id,
                'team_id' => $team->id,
            ]);

            DB::commit();

            // Log the user in
            Auth::login($user);
            $request->session()->regenerate();
            session()->flash('register_success_popup', 'Pendaftaran tim futsal "' . $team->name . '" berhasil! Selamat bergabung.');

            return $this->redirectUser();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan sistem saat mendaftar. Silakan coba lagi.',
            ]);
        }
    }

    protected function redirectUser()
    {
        $user = Auth::user();
        $slug = $user->isSuperAdmin() ? 'superadmin' : ($user->slug ?? 'user');
        
        $fallbackUrl = route($user->isSuperAdmin() ? 'superadmin.dashboard' : 'dashboard', ['slug' => $slug]);
        return redirect()->intended($fallbackUrl);
    }

    public function showLinkRequestForm()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Alamat email tidak terdaftar dalam sistem.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is locked
        if ($user->is_locked) {
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan atau ditutup. Silakan hubungi admin.',
            ]);
        }

        // Generate secure token
        $token = \Illuminate\Support\Str::random(60);

        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send reset link email
        try {
            Mail::to($request->email)->send(new \App\Mail\ResetPasswordMail($token, $request->email, $user));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email reset password: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Gagal mengirim email reset password. Silakan coba beberapa saat lagi.',
            ]);
        }

        return back()->with('status', 'Kami telah mengirimkan link reset password ke email Anda.');
    }

    public function showResetForm(Request $request, $token)
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        $email = $request->query('email');
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau telah kedaluwarsa.']);
        }

        // Check if token is older than 60 minutes
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token reset password telah kedaluwarsa. Silakan ajukan kembali.']);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->is_locked) {
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan atau ditutup.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil disetel ulang! Silakan masuk.');
    }
}
