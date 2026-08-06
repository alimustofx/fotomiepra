<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students_photos', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique()->index();
            $table->string('nama');
            $table->string('kelas')->index();
            $table->string('tahun_ajaran')->nullable();
            $table->string('drive_file_id');
            $table->text('drive_link_original');
            $table->text('drive_link_preview');
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_photos');
    }
};
