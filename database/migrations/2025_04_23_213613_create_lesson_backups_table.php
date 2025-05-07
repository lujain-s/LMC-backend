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
        Schema::create('lesson_backups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CourseId');
            $table->string('Title')->nullable();
            $table->date('Date');
            $table->time('Start_Time');
            $table->time('End_Time');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_backups');
    }
};
