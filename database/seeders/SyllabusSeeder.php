<?php

namespace Database\Seeders;

use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Database\Seeder;

class SyllabusSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $syllabi = [
            [
                'name'           => 'Pemrograman Web Dasar',
                'description'    => 'Mempelajari dasar-dasar pemrograman web seperti HTML, CSS, dan JavaScript.',
                'duration_weeks' => 12,
                'theme'          => null,
                'created_by'     => $admin->id,
            ],
            [
                'name'           => 'Basis Data',
                'description'    => 'Mempelajari konsep dan implementasi basis data relasional menggunakan MySQL.',
                'duration_weeks' => 10,
                'theme'          => null,
                'created_by'     => $admin->id,
            ],
            [
                'name'           => 'Pemrograman Berorientasi Objek',
                'description'    => 'Mempelajari konsep OOP menggunakan bahasa pemrograman PHP.',
                'duration_weeks' => 8,
                'theme'          => null,
                'created_by'     => $admin->id,
            ],
        ];

        foreach ($syllabi as $syllabus) {
            Syllabus::create($syllabus);
        }
    }
}
