<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('team_id', Auth::user()->team_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isManagement() && !Auth::user()->isCoach()) {
            return back()->with('error', 'Hanya Manajer atau Pelatih yang dapat membuat pengumuman.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Announcement::create([
            'team_id' => Auth::user()->team_id,
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Pengumuman berhasil diposting!');
    }
}
