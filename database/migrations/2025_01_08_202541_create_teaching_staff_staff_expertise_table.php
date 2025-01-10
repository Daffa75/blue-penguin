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
        Schema::create('teaching_staff_staff_expertise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_staff_id')->constrained('teaching_staff')->cascadeOnDelete();
            $table->foreignId('staff_expertise_id')->constrained('staff_expertises')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_staff_staff_expertise');
    }
};
