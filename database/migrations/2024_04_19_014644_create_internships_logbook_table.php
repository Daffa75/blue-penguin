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
        Schema::create('internships_logbook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->date('date');
            $table->text('activity');
            $table->text('result');
            $table->text('feedback')->nullable();
            $table->text('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships_logbook');
    }
};
