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
        Schema::create('department_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained(
                table: 'users', indexName: 'department_events_created_by'
            );
            $table->foreignId('updated_by')->constrained(
                table: 'users', indexName: 'department_events_updated_by'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_events');
    }
};
