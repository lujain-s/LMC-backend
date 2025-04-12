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
        Schema::create('placemnttests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('GuestId')->constrained('users');
            $table->foreignId('LanguageId')->constrained('languages');
            $table->string('Level');
            $table->float('AudioScore');
            $table->float('ReadingScore');
            $table->float('SpeakingScore');
            $table->float('TotalScore');
            $table->enum('Status', ['Pending','Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placemnttests');
    }
};
