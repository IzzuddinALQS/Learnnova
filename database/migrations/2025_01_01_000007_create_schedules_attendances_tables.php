<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->enum('type', ['online', 'offline', 'hybrid'])->default('online');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date')->nullable();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->text('note')->nullable();
            $table->timestamp('recorded_at')->nullable()->useCurrent();
            $table->timestamps();

            $table->unique(['schedule_id', 'student_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('schedules');
    }
};
