<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumPost;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ForumController extends Controller
{
    /**
     * Display a listing of threads for a course
     */
    public function index(Request $request, $courseId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.view')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($courseId);
        $filter = $request->get('filter', 'all');

        $query = ForumThread::where('course_id', $courseId)
            ->with(['user', 'posts'])
            ->withCount('posts');

        // Apply filters
        switch ($filter) {
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            case 'recent':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'pinned':
                $query->where('is_pinned', true);
                break;
            default:
                // Show pinned first, then by updated_at
                $query->orderBy('is_pinned', 'desc')
                      ->orderBy('updated_at', 'desc');
        }

        $threads = $query->paginate(15);

        return view('forum.index', compact('course', 'threads', 'filter'));
    }

    /**
     * Show the form for creating a new thread
     */
    public function create($courseId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.post')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($courseId);
        return view('forum.create', compact('course'));
    }

    /**
     * Store a newly created thread
     */
    public function store(Request $request, $courseId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.post')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = validator($request->all(), [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('forum/threads', 'public');
        }

        $thread = ForumThread::create([
            'course_id' => $courseId,
            'user_id'   => Auth::id(),
            'title'     => $request->title,
            'content'   => $request->content,
            'image'     => $imagePath,
        ]);

        return response()->json([
            'message'  => 'Thread berhasil dibuat.',
            'redirect' => route('forum.show', [$courseId, $thread->id]),
        ]);
    }

    /**
     * Display the specified thread
     */
    public function show($courseId, $threadId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.view')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($courseId);
        $thread = ForumThread::with(['user', 'posts' => function ($query) {
            $query->whereNull('parent_id')
                  ->with(['user', 'replies.user', 'replies.replies.user'])
                  ->orderBy('is_solution', 'desc')
                  ->orderBy('created_at', 'asc');
        }])->findOrFail($threadId);

        // Increment view counter
        $thread->incrementViews();

        return view('forum.show', compact('course', 'thread'));
    }

    /**
     * Toggle pin status of a thread
     */
    public function togglePin($courseId, $threadId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.moderate')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $thread = ForumThread::findOrFail($threadId);
        $thread->is_pinned = !$thread->is_pinned;
        $thread->save();

        $status = $thread->is_pinned ? 'dipasang' : 'dilepas';
        return response()->json(['message' => "Thread berhasil {$status} pin."]);
    }

    /**
     * Toggle lock status of a thread
     */
    public function toggleLock($courseId, $threadId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.moderate')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $thread = ForumThread::findOrFail($threadId);
        $thread->is_locked = !$thread->is_locked;
        $thread->save();

        $status = $thread->is_locked ? 'dikunci' : 'dibuka';
        return response()->json(['message' => "Thread berhasil {$status}."]);
    }

    /**
     * Remove the specified thread
     */
    public function destroy($courseId, $threadId)
    {
        $thread = ForumThread::findOrFail($threadId);

        // Check permission: owner or moderator
        if ($thread->user_id !== Auth::id() && !Auth::user()->hasPermission('forum.moderate')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // Soft delete thread and all posts
        DB::transaction(function () use ($thread) {
            ForumPost::where('thread_id', $thread->id)->delete();
            $thread->delete();
        });

        return response()->json(['message' => 'Thread berhasil dihapus.']);
    }

    /**
     * Store a new post/reply
     */
    public function storePost(Request $request, $courseId, $threadId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.post')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $thread = ForumThread::findOrFail($threadId);

        // Check if thread is locked
        if ($thread->is_locked) {
            return response()->json(['message' => 'Thread ini sudah dikunci.'], 403);
        }

        $validator = validator($request->all(), [
            'content'   => 'required|string',
            'parent_id' => 'nullable|exists:forum_posts,id',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('forum/posts', 'public');
        }

        $post = ForumPost::create([
            'thread_id' => $threadId,
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id ?: null,
            'content'   => $request->content,
            'image'     => $imagePath,
        ]);

        $post->load('user');

        // Update thread updated_at
        $thread->touch();

        $course = Course::findOrFail($courseId);

        // Render HTML fragment untuk inject ke DOM tanpa reload
        $html = view('forum._post_single', compact('post', 'thread', 'course'))->render();

        return response()->json([
            'message'   => 'Balasan berhasil ditambahkan.',
            'html'      => $html,
            'post_id'   => $post->id,
            'parent_id' => $post->parent_id,
        ]);
    }

    /**
     * Mark a post as solution
     */
    public function markSolution($courseId, $threadId, $postId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.moderate')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        DB::transaction(function () use ($threadId, $postId) {
            // Unmark all posts in thread
            ForumPost::where('thread_id', $threadId)->update(['is_solution' => false]);
            
            // Mark selected post as solution
            $post = ForumPost::findOrFail($postId);
            $post->is_solution = true;
            $post->save();
        });

        return response()->json(['message' => 'Post berhasil ditandai sebagai solusi.']);
    }

    /**
     * Unmark a post as solution
     */
    public function unmarkSolution($courseId, $threadId, $postId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('forum.moderate')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $post = ForumPost::findOrFail($postId);
        $post->is_solution = false;
        $post->save();

        return response()->json(['message' => 'Post berhasil dihapus dari solusi.']);
    }

    /**
     * Remove the specified post
     */
    public function destroyPost($courseId, $threadId, $postId)
    {
        $post = ForumPost::findOrFail($postId);

        // Check permission: owner or moderator
        if ($post->user_id !== Auth::id() && !Auth::user()->hasPermission('forum.moderate')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // Soft delete post and all nested replies
        DB::transaction(function () use ($post) {
            $this->deletePostAndReplies($post);
        });

        return response()->json([
            'message' => 'Post berhasil dihapus.',
            'post_id' => $postId,
        ]);
    }

    /**
     * Recursively delete post and its replies
     */
    private function deletePostAndReplies(ForumPost $post)
    {
        foreach ($post->replies as $reply) {
            $this->deletePostAndReplies($reply);
        }
        $post->delete();
    }
}
