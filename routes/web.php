<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SyllabusController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
 
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('syllabus', SyllabusController::class);

    // Forum routes (nested under courses)
    Route::prefix('courses/{course}')->name('forum.')->group(function () {
        Route::get('forum', [ForumController::class, 'index'])->name('index');
        Route::get('forum/create', [ForumController::class, 'create'])->name('create');
        Route::post('forum', [ForumController::class, 'store'])->name('store');
        Route::get('forum/{thread}', [ForumController::class, 'show'])->name('show');
        Route::delete('forum/{thread}', [ForumController::class, 'destroy'])->name('destroy');
        Route::post('forum/{thread}/pin', [ForumController::class, 'togglePin'])->name('togglePin');
        Route::post('forum/{thread}/lock', [ForumController::class, 'toggleLock'])->name('toggleLock');
        
        // Forum Posts / Replies
        Route::post('forum/{thread}/posts', [ForumController::class, 'storePost'])->name('storePost');
        Route::delete('forum/{thread}/posts/{post}', [ForumController::class, 'destroyPost'])->name('destroyPost');
        Route::post('forum/{thread}/posts/{post}/solution', [ForumController::class, 'markSolution'])->name('markSolution');
        Route::delete('forum/{thread}/posts/{post}/solution', [ForumController::class, 'unmarkSolution'])->name('unmarkSolution');
    });
});