<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasPermission('announcements.manage')) {
            $announcements = Announcement::with(['creator', 'course'])
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $announcements = Announcement::with('course')
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderByDesc('published_at')
                ->get();
        }

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasPermission('announcements.manage')) {
            abort(403);
        }

        $courses = $user->hasRole('pengajar')
            ? $user->taughtCourses()->orderBy('title')->get()
            : Course::orderBy('title')->get();

        return view('announcements.form', compact('courses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermission('announcements.manage')) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'is_published' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['is_published'] = $request->has('is_published');
        $data['published_at'] = $data['is_published'] && empty($data['published_at']) ? now() : $data['published_at'];
        $data['created_by'] = $user->id;

        Announcement::create($data);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function show(Announcement $announcement)
    {
        $user = Auth::user();

        if (!$user->hasPermission('announcements.manage')) {
            if (! $announcement->is_published || ! $announcement->published_at || $announcement->published_at->isFuture()) {
                abort(404);
            }
        }

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('announcements.manage')) {
            abort(403);
        }

        $courses = $user->hasRole('pengajar')
            ? $user->taughtCourses()->orderBy('title')->get()
            : Course::orderBy('title')->get();

        return view('announcements.form', compact('announcement', 'courses'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('announcements.manage')) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'is_published' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['is_published'] = $request->has('is_published');
        $data['published_at'] = $data['is_published'] && empty($data['published_at']) ? now() : $data['published_at'];

        $announcement->update($data);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('announcements.manage')) {
            abort(403);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
