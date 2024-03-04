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
        Schema::create('curriculum_structures_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained(
                table: 'curriculum_structures', indexName: 'curriculum_id'
            )
            ->onDelete('cascade');
            $table->string('semester_name');
            $table->unsignedInteger('credit_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_structures_semesters');
    }
};
