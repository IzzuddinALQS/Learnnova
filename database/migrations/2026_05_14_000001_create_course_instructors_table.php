<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false)->comment('Pengajar utama / PIC kelas');
            $table->timestamps();
            $table->unique(['course_id', 'user_id']);
        });

        // Migrasi data lama: pindahkan instructor_id yang sudah ada ke tabel pivot
        if (Schema::hasColumn('courses', 'instructor_id')) {
            DB::statement('
                INSERT INTO course_instructors (course_id, user_id, is_primary, created_at, updated_at)
                SELECT id, instructor_id, 1, NOW(), NOW()
                FROM courses
                WHERE instructor_id IS NOT NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_instructors');
    }
};
