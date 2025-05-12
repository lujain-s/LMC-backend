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
        Schema::create('usertasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('UserId')->constrained('users');
            $table->foreignId('TaskId')->constrained('tasks');
            $table->boolean('RequiresInvoice')->default(false);
            $table->boolean('Completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usertasks');
    }
};
