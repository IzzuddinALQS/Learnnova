<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('position');
            $table->string('signature_path', 500);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique('course_id');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('certificate_signer_id')->nullable()->after('student_id')->constrained('certificate_signers')->nullOnDelete();
            $table->string('signer_name')->nullable()->after('certificate_no');
            $table->string('signer_position')->nullable()->after('signer_name');
            $table->string('signature_path', 500)->nullable()->after('signer_position');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('certificate_signer_id');
            $table->dropColumn(['signer_name', 'signer_position', 'signature_path']);
        });

        Schema::dropIfExists('certificate_signers');
    }
};
