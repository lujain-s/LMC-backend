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
        Schema::create('enrollment_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CourseId');
            $table->date('Enroll_Date');
            $table->timestamps();

            $table->foreign('CourseId')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['CourseId', 'Enroll_Date']); // منع التكرار
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_days');
    }
};
