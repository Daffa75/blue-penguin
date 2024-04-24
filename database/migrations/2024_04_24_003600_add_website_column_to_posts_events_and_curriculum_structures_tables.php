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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('website')->nullable()->after('language');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('website')->nullable()->after('description');
        });

        Schema::table('curriculum_structures', function (Blueprint $table) {
            $table->string('website')->nullable()->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('website');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('website');
        });

        Schema::table('curriculum_structures', function (Blueprint $table) {
            $table->dropColumn('website');
        });
    }
};
