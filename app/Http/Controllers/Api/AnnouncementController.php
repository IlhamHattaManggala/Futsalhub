<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $announcements = Announcement::where('team_id', $user->team_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengumuman berhasil diambil.',
            'data' => [
                'announcements' => $announcements->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'title' => $a->title,
                        'content' => $a->content,
                        'author_name' => $a->user ? $a->user->name : 'Unknown',
                        'created_at' => $a->created_at
                    ];
                })
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isCoach() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer atau Pelatih yang dapat membuat pengumuman.'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diposting!',
            'data' => [
                'announcement' => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'author_name' => $user->name,
                    'created_at' => $announcement->created_at
                ]
            ]
        ], 201);
    }
}
