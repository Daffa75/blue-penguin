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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained(
                table: 'curriculum_structures_semesters', indexName: 'semester_id'
            )
            ->cascadeOnDelete();
            $table->string('module_code')->nullable();
            $table->string('module_name');
            $table->unsignedInteger('credit_points');
            $table->text('module_handbook');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
