<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MateriHubController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AssignmentSubmissionController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/profile', [UserController::class, 'profile']);
Route::post('/profile/update', [UserController::class, 'updateProfile']);


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
    Route::resource('assignments', AssignmentController::class);
    Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('assignments/{assignment}/submissions', [AssignmentController::class, 'submissions'])->name('assignments.submissions');
    Route::get('assignments/{assignment}/submissions/{submission}', [AssignmentController::class, 'gradeForm'])->name('assignments.grade.form');
    Route::post('assignments/{assignment}/submissions/{submission}', [AssignmentController::class, 'grade'])->name('assignments.grade');
    Route::post('/notifications/{id}/read', function ($id) {
        Auth::user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    })->middleware('auth')->name('notifications.read');

    
    // Enrollment — manajemen pelajar & pengajar per kelas
    Route::prefix('courses/{course}/enrollments')->name('enrollments.')->group(function () {
        Route::get('/',                        [EnrollmentController::class, 'index'])->name('index');
        Route::post('/',                       [EnrollmentController::class, 'store'])->name('store');
        Route::put('/{enrollment}',            [EnrollmentController::class, 'update'])->name('update');
        Route::delete('/{enrollment}',         [EnrollmentController::class, 'destroy'])->name('destroy');
    });
    // Overview pelajar & pengajar (akademik)
    Route::get('/students',  [EnrollmentController::class, 'studentOverview'])->name('students.overview');
    Route::get('/teachers',  [EnrollmentController::class, 'teacherOverview'])->name('teachers.overview');

    // Hub materi — entry point dari sidebar
    Route::get('/materi', [MateriHubController::class, 'index'])->name('materi.hub');

    // Forum global (langsung dari sidebar)
    Route::get('/forum', [ForumController::class, 'globalIndex'])->name('forum.global');

    // Forum routes per kelas
    Route::prefix('courses/{courseId}/forum')->name('forum.')->group(function () {
        Route::get('/',          [ForumController::class, 'index'])->name('index');
        Route::get('/create',    [ForumController::class, 'create'])->name('create');
        Route::post('/',         [ForumController::class, 'store'])->name('store');
        Route::get('/{threadId}', [ForumController::class, 'show'])->name('show');
        Route::delete('/{threadId}', [ForumController::class, 'destroy'])->name('destroy');
        Route::post('/{threadId}/pin',  [ForumController::class, 'togglePin'])->name('togglePin');
        Route::post('/{threadId}/lock', [ForumController::class, 'toggleLock'])->name('toggleLock');

        // Post/reply routes
        Route::post('/{threadId}/posts',                          [ForumController::class, 'storePost'])->name('storePost');
        Route::delete('/{threadId}/posts/{postId}',               [ForumController::class, 'destroyPost'])->name('destroyPost');
        Route::post('/{threadId}/posts/{postId}/solution',        [ForumController::class, 'markSolution'])->name('markSolution');
        Route::post('/{threadId}/posts/{postId}/unmark-solution', [ForumController::class, 'unmarkSolution'])->name('unmarkSolution');
    });

    // Materi & Modul routes per kelas
    Route::prefix('courses/{courseId}')->name('courses.')->group(function () {
        // Halaman utama materi (index modul)
        Route::get('/materials', [CourseModuleController::class, 'index'])->name('materials.index');

        // CRUD CourseModule
        Route::post('/modules',            [CourseModuleController::class, 'store'])->name('modules.store');
        Route::put('/modules/{module}',    [CourseModuleController::class, 'update'])->name('modules.update');
        Route::delete('/modules/{module}', [CourseModuleController::class, 'destroy'])->name('modules.destroy');

        // CRUD Material — create/store must come before {material} to avoid route conflict
        Route::get('/materials/create',          [MaterialController::class, 'create'])->name('materials.create');
        Route::post('/materials',                [MaterialController::class, 'store'])->name('materials.store');
        Route::get('/materials/{material}',      [MaterialController::class, 'show'])->name('materials.show');
        Route::get('/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
        Route::put('/materials/{material}',      [MaterialController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{material}',   [MaterialController::class, 'destroy'])->name('materials.destroy');

        // Progress & Bookmark (AJAX only)
        Route::post('/materials/{material}/progress', [MaterialController::class, 'progress'])->name('materials.progress');
        Route::post('/materials/{material}/bookmark', [MaterialController::class, 'bookmark'])->name('materials.bookmark');
    });
});