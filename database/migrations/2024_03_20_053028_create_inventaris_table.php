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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('lecturers');
            $table->string('name');
            $table->date('date');
            $table->bigInteger('price');
            $table->enum('condition', ['Baik', 'Rusak Ringan', 'Rusak Berat']);
            $table->integer('quantity');
            $table->string('registration_number');
            $table->boolean('is_found');
            $table->boolean('is_used');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
