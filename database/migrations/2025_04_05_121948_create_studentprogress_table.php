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
        Schema::create('studentprogress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('StudentId')->constrained('users');
            $table->foreignId('CourseId')->constrained('courses');
            $table->float('Percentage');
            $table->float('FinalScore');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studentprogress');
    }
};
