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
        Schema::table('teaching_staff', function (Blueprint $table) {
            // Drop existing columns
            $table->dropColumn(['expertise_en', 'expertise_idn', 'link']);

            // Add new columns
            $table->string('handbook_link')->nullable();
            $table->string('scholar_link')->nullable();
            $table->string('scopus_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_staff', function (Blueprint $table) {
            // Add dropped columns back
            $table->string('expertise_en')->nullable(false);
            $table->string('expertise_idn')->nullable(false);
            $table->string('link')->nullable(false);

            // Drop newly added columns
            $table->dropColumn(['handbook_link', 'scholar_link', 'scopus_link']);
        });
    }
};
