<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $pengajar = User::whereHas('roles', fn($q) => $q->where('name', 'pengajar'))->first();
        $courses  = Course::all();

        if (!$pengajar || $courses->isEmpty()) {
            $this->command->warn('Tidak ada pengajar atau course. AssignmentSeeder dilewati.');
            return;
        }

        $assignments = [
            [
                'title'       => 'Tugas 1 - Pengenalan HTML',
                'description' => 'Buatlah halaman HTML sederhana yang berisi biodata diri Anda menggunakan tag-tag dasar HTML seperti heading, paragraph, list, dan image.',
                'due_date'    => now()->addDays(7),
                'max_score'   => 100,
                'file'        => null,
            ],
            [
                'title'       => 'Tugas 2 - CSS Styling',
                'description' => 'Tambahkan styling CSS pada halaman HTML yang telah dibuat sebelumnya. Gunakan selector, properties, dan nilai yang sesuai.',
                'due_date'    => now()->addDays(14),
                'max_score'   => 100,
                'file'        => null,
            ],
            [
                'title'       => 'Tugas 3 - Javascript Dasar',
                'description' => 'Buatlah program Javascript sederhana yang menampilkan form input dan memvalidasi data yang dimasukkan pengguna.',
                'due_date'    => now()->addDays(21),
                'max_score'   => 100,
                'file'        => null,
            ],
        ];

        foreach ($assignments as $i => $data) {
            $course = $courses->get($i % $courses->count());

            Assignment::create(array_merge($data, [
                'course_id'  => $course->id,
                'created_by' => $pengajar->id,
            ]));
        }
    }
}
