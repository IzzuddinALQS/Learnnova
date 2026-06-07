<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Roles — upsert agar aman dijalankan ulang ─────────────────
        $roles = [
            ['name' => 'super_admin', 'guard_name' => 'web', 'description' => 'Super Administrator - akses penuh semua fitur'],
            ['name' => 'akademik',    'guard_name' => 'web', 'description' => 'Staf Akademik - kelola data administratif'],
            ['name' => 'pengajar',    'guard_name' => 'web', 'description' => 'Pengajar / Instruktur kelas'],
            ['name' => 'pelajar',     'guard_name' => 'web', 'description' => 'Pelajar / Siswa'],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                ['description' => $roleData['description']]
            );
        }

        // ── 2. Permissions — upsert agar permission baru bisa ditambah ───
        $permissions = [
            // Users
            ['name' => 'users.view',    'module' => 'users',         'description' => 'Lihat daftar pengguna'],
            ['name' => 'users.create',  'module' => 'users',         'description' => 'Tambah pengguna baru'],
            ['name' => 'users.edit',    'module' => 'users',         'description' => 'Edit data pengguna'],
            ['name' => 'users.delete',  'module' => 'users',         'description' => 'Hapus pengguna'],
            // Roles
            ['name' => 'roles.view',    'module' => 'roles',         'description' => 'Lihat daftar role'],
            ['name' => 'roles.create',  'module' => 'roles',         'description' => 'Buat role baru'],
            ['name' => 'roles.edit',    'module' => 'roles',         'description' => 'Edit role dan permission'],
            ['name' => 'roles.delete',  'module' => 'roles',         'description' => 'Hapus role'],
            // Courses
            ['name' => 'courses.view',    'module' => 'courses',     'description' => 'Lihat semua kelas'],
            ['name' => 'courses.create',  'module' => 'courses',     'description' => 'Buat kelas baru'],
            ['name' => 'courses.edit',    'module' => 'courses',     'description' => 'Edit kelas'],
            ['name' => 'courses.delete',  'module' => 'courses',     'description' => 'Hapus kelas'],
            ['name' => 'courses.publish', 'module' => 'courses',     'description' => 'Publish/unpublish kelas'],
            // Materials
            ['name' => 'materials.view',   'module' => 'materials',  'description' => 'Akses materi pembelajaran'],
            ['name' => 'materials.create', 'module' => 'materials',  'description' => 'Upload materi baru'],
            ['name' => 'materials.edit',   'module' => 'materials',  'description' => 'Edit materi'],
            ['name' => 'materials.delete', 'module' => 'materials',  'description' => 'Hapus materi'],
            // Assignments
            ['name' => 'assignments.view',   'module' => 'assignments', 'description' => 'Lihat tugas'],
            ['name' => 'assignments.create', 'module' => 'assignments', 'description' => 'Buat tugas'],
            ['name' => 'assignments.edit',   'module' => 'assignments', 'description' => 'Edit tugas'],
            ['name' => 'assignments.delete', 'module' => 'assignments', 'description' => 'Hapus tugas'],
            ['name' => 'assignments.grade',  'module' => 'assignments', 'description' => 'Beri nilai tugas'],
            ['name' => 'assignments.submit', 'module' => 'assignments', 'description' => 'Kumpulkan tugas (pelajar)'],
            // Quizzes
            ['name' => 'quizzes.view',   'module' => 'quizzes',     'description' => 'Lihat quiz'],
            ['name' => 'quizzes.create', 'module' => 'quizzes',     'description' => 'Buat quiz'],
            ['name' => 'quizzes.edit',   'module' => 'quizzes',     'description' => 'Edit quiz'],
            ['name' => 'quizzes.delete', 'module' => 'quizzes',     'description' => 'Hapus quiz'],
            ['name' => 'quizzes.take',   'module' => 'quizzes',     'description' => 'Ikuti quiz (pelajar)'],
            // Forum
            ['name' => 'forum.view',     'module' => 'forum',       'description' => 'Lihat forum diskusi'],
            ['name' => 'forum.post',     'module' => 'forum',       'description' => 'Posting di forum'],
            ['name' => 'forum.moderate', 'module' => 'forum',       'description' => 'Moderasi forum'],
            // Schedules
            ['name' => 'schedules.view',   'module' => 'schedules', 'description' => 'Lihat jadwal'],
            ['name' => 'schedules.manage', 'module' => 'schedules', 'description' => 'Kelola jadwal'],
            // Attendance
            ['name' => 'attendance.view',   'module' => 'attendance', 'description' => 'Lihat absensi'],
            ['name' => 'attendance.manage', 'module' => 'attendance', 'description' => 'Kelola absensi'],
            // Reports & Certificates
            ['name' => 'reports.view',       'module' => 'reports',       'description' => 'Lihat laporan akademik'],
            ['name' => 'reports.export',     'module' => 'reports',       'description' => 'Export nilai'],
            ['name' => 'certificates.manage','module' => 'certificates',  'description' => 'Kelola sertifikat'],
            // Announcements
            ['name' => 'announcements.view',   'module' => 'announcements', 'description' => 'Lihat pengumuman'],
            ['name' => 'announcements.manage', 'module' => 'announcements', 'description' => 'Buat dan kelola pengumuman'],
            // Misc
            ['name' => 'notifications.manage', 'module' => 'notifications', 'description' => 'Atur notifikasi sistem'],
            ['name' => 'activity.view',        'module' => 'activity',      'description' => 'Monitor aktivitas sistem'],
            ['name' => 'syllabus.view',        'module' => 'syllabus',      'description' => 'Lihat silabus'],
            ['name' => 'syllabus.manage',      'module' => 'syllabus',      'description' => 'Kelola silabus'],
        ];

        foreach ($permissions as $permData) {
            Permission::updateOrCreate(
                ['name' => $permData['name'], 'guard_name' => 'web'],
                [
                    'module'      => $permData['module'],
                    'description' => $permData['description'],
                    'guard_name'  => 'web',
                ]
            );
        }

        // ── 3. Assign permissions ke role — sync agar tidak duplikat ─────

        // Helper: ambil permission IDs by name, lalu sync ke role
        $syncRole = function (string $roleName, array $permNames) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) return;

            $permIds = Permission::whereIn('name', $permNames)->pluck('id');
            // syncWithoutDetaching = tambah yang baru, tidak hapus yang lama
            // sync = replace total (lebih predictable untuk seeder)
            $role->permissions()->sync($permIds);
        };

        // super_admin — semua permission
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::pluck('id'));
        }

        // akademik
        $syncRole('akademik', [
            'users.view', 'users.create', 'users.edit',
            'roles.view',
            'courses.view', 'courses.create', 'courses.edit', 'courses.delete', 'courses.publish',
            'materials.view',
            'schedules.view', 'schedules.manage',
            'attendance.view',
            'reports.view', 'reports.export',
            'certificates.manage',
            'announcements.view', 'announcements.manage',
            'notifications.manage',
            'activity.view',
            'syllabus.view', 'syllabus.manage',
            'forum.view', 'forum.moderate',
        ]);

        // pengajar
        $syncRole('pengajar', [
            'courses.view', 'courses.edit',
            'materials.view', 'materials.create', 'materials.edit', 'materials.delete',
            'assignments.view', 'assignments.create', 'assignments.edit', 'assignments.delete', 'assignments.grade',
            'quizzes.view', 'quizzes.create', 'quizzes.edit', 'quizzes.delete',
            'forum.view', 'forum.post', 'forum.moderate',
            'schedules.view', 'schedules.manage',
            'attendance.view', 'attendance.manage',
            'reports.view', 'reports.export',
            'announcements.view', 'announcements.manage',
        ]);

        // pelajar
        $syncRole('pelajar', [
            'courses.view',
            'materials.view',
            'assignments.view', 'assignments.submit',
            'quizzes.view', 'quizzes.take',
            'forum.view', 'forum.post',
            'schedules.view',
            'attendance.view',
            'announcements.view',
        ]);
    }
}
