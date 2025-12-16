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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_class_subject_id');
            $table->unsignedBigInteger('semester_id');
            $table->string('tipe');
            $table->string('judul');
            $table->date('tanggal');
            $table->string('status');
            $table->text('approval_note')->nullable();
            $table->timestamps();

            $table->foreign('teacher_class_subject_id')->references('id')->on('teacher_class_subjects')->cascadeOnUpdate();
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
