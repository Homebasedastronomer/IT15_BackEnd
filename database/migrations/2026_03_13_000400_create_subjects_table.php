<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('title');
            $table->unsignedTinyInteger('units')->default(3);
            $table->unsignedTinyInteger('year_level')->default(1);
            $table->string('offered_in', 60);
            $table->string('term_indicator', 30)->default('Per Semester');
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->json('prerequisites')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'year_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
