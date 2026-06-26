<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('assignment_weight')->default(60)->after('max_students');
            $table->unsignedInteger('quiz_weight')->default(40)->after('assignment_weight');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['assignment_weight', 'quiz_weight']);
        });
    }
};
