<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'file')) {
                $table->string('file')->nullable()->after('max_score');
            }
            if (!Schema::hasColumn('assignments', 'created_by')) {
<<<<<<< Updated upstream
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('file');
=======
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
>>>>>>> Stashed changes
            }
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('assignments', 'file')) {
                $table->dropColumn('file');
            }
        });
    }
};
