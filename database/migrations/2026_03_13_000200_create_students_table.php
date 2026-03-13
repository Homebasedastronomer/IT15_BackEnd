<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number', 30)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('gender', 20);
            $table->date('birth_date')->nullable();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedTinyInteger('year_level')->default(1);
            $table->string('status', 30)->default('Enrolled');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'year_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
