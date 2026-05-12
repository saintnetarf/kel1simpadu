<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('class_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained('study_programs')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('participant_status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique([
                'academic_year_id',
                'study_program_id',
                'class_id',
                'course_id',
                'student_id',
            ], 'class_participants_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_participants');
    }
};
