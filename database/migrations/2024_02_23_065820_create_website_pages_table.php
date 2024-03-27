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
        Schema::create('website_pages', function (Blueprint $table) {
            $table->id('page_id');
            $table->string('page');
            $table->string('slug');
            $table->text('content_en');
            $table->text('content_id');
            $table->foreignId('created_by')->constrained(
                table: 'users', indexName: 'website_pages_created_by'
            );
            $table->foreignId('updated_by')->constrained(
                table: 'users', indexName: 'website_pages_updated_by'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_pages');
    }
};
