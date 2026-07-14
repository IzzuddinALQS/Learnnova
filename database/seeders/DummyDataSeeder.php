<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Generate 15 Students
        $students = [];
        for ($i = 0; $i < 15; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'is_active' => 1,
            ]);
            
            DB::table('role_user')->insert([
                'role_id' => 4, // Pelajar
                'model_id' => $user->id,
                'model_type' => 'App\\Models\\User',
            ]);
            $students[] = $user->id;
        }

        // 2. Generate 2 Instructors
        $instructors = [];
        for ($i = 0; $i < 2; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'is_active' => 1,
            ]);
            
            DB::table('role_user')->insert([
                'role_id' => 3, // Pengajar
                'model_id' => $user->id,
                'model_type' => 'App\\Models\\User',
            ]);
            $instructors[] = $user->id;
        }

        // 3. Generate Courses, Modules, Materials, Assignments, Quizzes
        $courses = Course::factory()->count(10)->create([
            'instructor_id' => function() use ($faker, $instructors) {
                return $faker->randomElement($instructors);
            }
        ]);

        foreach ($courses as $course) {
            // Modules
            $modules = CourseModule::factory()->count(3)->create(['course_id' => $course->id]);
            
            foreach ($modules as $module) {
                // Materials
                Material::factory()->count(2)->create(['module_id' => $module->id]);
            }

            // Assignment
            Assignment::factory()->create([
                'course_id' => $course->id,
                'created_by' => $course->instructor_id
            ]);

            // Quiz
            $quiz = Quiz::factory()->create(['course_id' => $course->id]);

            // Questions for Quiz
            $questions = QuizQuestion::factory()->count(5)->create(['quiz_id' => $quiz->id]);

            foreach ($questions as $question) {
                // 3 Wrong Options
                QuizOption::factory()->count(3)->create([
                    'question_id' => $question->id,
                    'is_correct' => false
                ]);
                // 1 Correct Option
                QuizOption::factory()->create([
                    'question_id' => $question->id,
                    'is_correct' => true
                ]);
            }

            // Schedule
            Schedule::factory()->count(2)->create([
                'course_id' => $course->id,
                'user_id' => $course->instructor_id
            ]);

            // Enroll 5-10 random students to this course
            $enrolledStudents = $faker->randomElements($students, $faker->numberBetween(5, 10));
            foreach ($enrolledStudents as $studentId) {
                DB::table('enrollments')->insertOrIgnore([
                    'course_id' => $course->id,
                    'student_id' => $studentId,
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
