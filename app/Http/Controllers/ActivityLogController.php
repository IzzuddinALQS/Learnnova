<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasPermission('activity.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = ActivityLog::with('user')->orderByDesc('created_at');

        // Filter by log_name / modul
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs     = $query->paginate(25)->withQueryString();
        $logNames = ActivityLog::select('log_name')->distinct()->orderBy('log_name')->pluck('log_name');
        $users    = User::orderBy('name')->get(['id', 'name']);

        return view('activity-logs.index', compact('logs', 'logNames', 'users'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        if (!Auth::user()->hasPermission('activity.view')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $activityLog->delete();

        return response()->json(['message' => 'Log berhasil dihapus.']);
    }

    public function destroyAll(Request $request)
    {
        if (!Auth::user()->hasPermission('activity.view')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        ActivityLog::query()->delete();

        return response()->json(['message' => 'Semua log berhasil dihapus.']);
    }
}
