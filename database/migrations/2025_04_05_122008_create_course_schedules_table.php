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
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('CourseId')->constrained('courses');
            $table->foreignId('RoomId')->nullable()->constrained('rooms')->nullOnDelete();
            $table->date('Start_Enroll');
            $table->date('End_Enroll');
            $table->enum('Enroll_Status', ['Open','Full'])->default('Open');
            $table->date('Start_Date');
            $table->date('End_Date');
            $table->time('Start_Time');
            $table->time('End_Time');
            $table->json('CourseDays');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_schedules');
    }
};
