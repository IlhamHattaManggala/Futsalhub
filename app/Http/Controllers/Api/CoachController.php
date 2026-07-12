<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoachController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer yang dapat mengakses daftar pelatih.'
            ], 403);
        }

        $coachRole = Role::where('name', 'coach')->firstOrFail();
        
        $coaches = User::where('team_id', $user->team_id)
            ->where('role_id', $coachRole->id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pelatih berhasil diambil.',
            'data' => [
                'coaches' => $coaches->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'email' => $c->email,
                        'role' => 'coach',
                        'slug' => $c->slug
                    ];
                })
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer yang dapat menambahkan pelatih.'
            ], 403);
        }

        $team = $user->team;
        if ($team && !$team->canAddCoach()) {
            return response()->json([
                'success' => false,
                'message' => 'Tim Anda telah mencapai batas maksimal 1 pelatih untuk paket Free. Silakan upgrade ke Premium.'
            ], 400);
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

        $coach = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $coachRole->id,
            'team_id' => $team->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pelatih berhasil ditambahkan!',
            'data' => [
                'coach' => [
                    'id' => $coach->id,
                    'name' => $coach->name,
                    'email' => $coach->email,
                    'role' => 'coach',
                    'slug' => $coach->slug
                ]
            ]
        ], 201);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer yang dapat menghapus pelatih.'
            ], 403);
        }

        $coachRole = Role::where('name', 'coach')->firstOrFail();

        $coach = User::where('team_id', $user->team_id)
            ->where('role_id', $coachRole->id)
            ->findOrFail($id);

        $coach->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelatih berhasil dihapus.'
        ]);
    }
}
