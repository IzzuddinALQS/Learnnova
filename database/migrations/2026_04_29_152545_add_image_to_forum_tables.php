<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->string('image', 500)->nullable()->after('content')
                  ->comment('Path gambar lampiran thread');
        });

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->string('image', 500)->nullable()->after('content')
                  ->comment('Path gambar lampiran post/reply');
        });
    }

    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
