<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::with('roles')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('users.create')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        return view('users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('users.create')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
        $validator = validator($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'phone'     => 'nullable|string|max:20',
            'role_id'   => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->roles()->attach($request->role_id, ['model_type' => User::class]);

        ActivityLog::log('User baru ditambahkan: ' . $user->name, 'users', $user);

        return response()->json([
            'message' => 'User berhasil ditambahkan.',
            'redirect' => route('users.index'),
        ]);
    }

    public function edit(User $user)
    {
        if (!Auth::user()->hasPermission('users.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        $user->load('roles');
        return view('users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('users.edit')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = validator($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
            'role_id'  => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'is_active' => $request->boolean('is_active'),
            'password'  => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        $user->roles()->sync([$request->role_id => ['model_type' => User::class]]);

        ActivityLog::log('User diperbarui: ' . $user->name, 'users', $user);

        return response()->json([
            'message' => 'User berhasil diupdate.',
            'redirect' => route('users.index'),
        ]);
    }

    public function destroy(User $user)
    {
        if (!Auth::user()->hasPermission('users.delete')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $user->delete();
        ActivityLog::log('User dihapus: ' . $user->name, 'users');
        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    public function profile()
{
    return view('profile');
}

public function updateProfile(Request $request)
{
    $user = auth()->user();

    $validator = validator($request->all(), [
        'name'     => 'required|string|max:255',
        'email'    => [
            'required',
            'email',
            Rule::unique('users')->ignore($user->id)
        ],
        'phone'    => 'nullable|string|max:20',
        'bio'      => 'nullable|string',
        'password' => 'nullable|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput();
    }

    $user->update([
        'name'     => $request->name,
        'email'    => $request->email,
        'phone'    => $request->phone,
        'bio'      => $request->bio,
        'password' => $request->password
            ? Hash::make($request->password)
            : $user->password,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Profile berhasil diperbarui.');
    }
}   
