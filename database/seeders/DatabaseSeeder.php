<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            UserSeeder::class,
            SyllabusSeeder::class,
            CourseForumSeeder::class,
            AssignmentSeeder::class
        ]);

        // Seed additional students for statistics
        $students = [
            ['name' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'password' => \Hash::make('admin123'), 'is_active' => 1],
            ['name' => 'Ani Wijaya', 'email' => 'ani@gmail.com', 'password' => \Hash::make('admin123'), 'is_active' => 1],
            ['name' => 'Citra Lestari', 'email' => 'citra@gmail.com', 'password' => \Hash::make('admin123'), 'is_active' => 1],
            ['name' => 'Dedi Kurniawan', 'email' => 'dedi@gmail.com', 'password' => \Hash::make('admin123'), 'is_active' => 1],
        ];

        foreach ($students as $student) {
            $studentId = \DB::table('users')->insertGetId(array_merge($student, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Assign role "pelajar" (role_id = 4)
            \DB::table('role_user')->insert([
                'role_id'    => 4,
                'model_id'   => $studentId,
                'model_type' => 'App\\Models\\User',
            ]);

            // Enroll in all 3 courses
            for ($courseId = 1; $courseId <= 3; $courseId++) {
                \DB::table('enrollments')->insertOrIgnore([
                    'course_id'  => $courseId,
                    'student_id' => $studentId,
                    'status'     => 'active',
                    'enrolled_at'=> now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Seed Quizzes for each course
        $quizzes = [
            ['id' => 1, 'course_id' => 1, 'title' => 'Quiz Dasar Laravel', 'passing_score' => 70.00],
            ['id' => 2, 'course_id' => 2, 'title' => 'Quiz Prinsip UI/UX', 'passing_score' => 70.00],
            ['id' => 3, 'course_id' => 3, 'title' => 'Quiz Machine Learning', 'passing_score' => 70.00],
        ];

        foreach ($quizzes as $quiz) {
            \DB::table('quizzes')->insertOrIgnore(array_merge($quiz, [
                'description' => 'Evaluasi pemahaman materi pelajaran.',
                'duration_minutes' => 30,
                'max_attempts' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed Quiz Attempts for all students (Pelajar ID: 4, and Budi/Ani/Citra/Dedi IDs: 6, 7, 8, 9)
        $studentScores = [
            // Student ID => [Quiz 1 score, Quiz 2 score, Quiz 3 score]
            4 => [85.00, 75.00, 90.00], // Pelajar
            6 => [95.00, 88.00, 92.00], // Budi
            7 => [65.00, 70.00, 80.00], // Ani
            8 => [75.00, 85.00, 85.00], // Citra
            9 => [45.00, 50.00, 60.00], // Dedi
        ];

        foreach ($studentScores as $studentId => $scores) {
            foreach ($scores as $index => $score) {
                $quizId = $index + 1;
                \DB::table('quiz_attempts')->insert([
                    'quiz_id'    => $quizId,
                    'student_id' => $studentId,
                    'score'      => $score,
                    'is_passed'  => $score >= 70,
                    'started_at' => now()->subMinutes(30),
                    'finished_at'=> now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
