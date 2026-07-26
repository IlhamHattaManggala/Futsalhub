<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoachController extends Controller
{
    public function index()
    {
        $teamId = Auth::user()->team_id;
        $coachRole = Role::where('name', 'coach')->firstOrFail();
        
        $coaches = User::where('team_id', $teamId)
            ->where('role_id', $coachRole->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('coaches.index', compact('coaches'));
    }

    public function store(Request $request)
    {
        $team = Auth::user()->team;
        if ($team && !$team->canAddCoach()) {
            return back()->withInput()->with('error', 'Tim Anda telah mencapai batas maksimal 1 pelatih untuk paket Free. Silakan upgrade ke Premium.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
        ]);

        $coachRole = Role::where('name', 'coach')->firstOrFail();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $coachRole->id,
            'team_id' => $team->id,
        ]);

        return back()->with('success', 'Pelatih berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $teamId = Auth::user()->team_id;
        $coachRole = Role::where('name', 'coach')->firstOrFail();

        $coach = User::where('team_id', $teamId)
            ->where('role_id', $coachRole->id)
            ->findOrFail($id);

        $coach->delete();

        return back()->with('success', 'Pelatih berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $teamId = Auth::user()->team_id;
        $coachRole = Role::where('name', 'coach')->firstOrFail();
        
        $coach = User::where('team_id', $teamId)
            ->where('role_id', $coachRole->id)
            ->findOrFail($id);

        // Prevent locking oneself
        if ($coach->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        $coach->is_locked = !$coach->is_locked;
        $coach->save();

        $statusStr = $coach->is_locked ? 'dinonaktifkan' : 'diaktifkan';
        return back()->with('success', 'Akun pelatih "' . $coach->name . '" berhasil ' . $statusStr . '.');
    }
}
