<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleEnrollmentWorkingDaysTable extends Migration
{
    public function up()
    {
        Schema::create('schedule_enrollment_working_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('course_schedules')->onDelete('cascade');
            $table->foreignId('holiday_id')->constrained('holidays')->onDelete('cascade');
            $table->json('working_days');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_enrollment_working_days');
    }
}

