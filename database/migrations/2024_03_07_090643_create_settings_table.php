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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('form_id');
            $table->foreign('form_id')->references('form_id')->on('forms')->onDelete('cascade');
            $table->text('page_header')->nullable();
            $table->text('page_outro')->nullable();
            $table->string('logo')->nullable();
            $table->string('fb_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('text_color')->nullable();
            $table->string('primary_color')->nullable();
            $table->json('rating')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
