<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHolidayIdToScheduleEnrollmentBackups extends Migration
{
    public function up()
    {
        Schema::table('schedule_enrollment_backups', function (Blueprint $table) {
            $table->unsignedBigInteger('holiday_id')->nullable()->after('id'); // Add holiday_id column
            $table->foreign('holiday_id')->references('id')->on('holidays')->onDelete('cascade'); // Add foreign key constraint
        });
    }

    public function down()
    {
        Schema::table('schedule_enrollment_backups', function (Blueprint $table) {
            $table->dropForeign(['holiday_id']);
            $table->dropColumn('holiday_id');
        });
    }
}
