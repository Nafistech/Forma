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
        Schema::create('fields', function (Blueprint $table) {
            $table->string('field_id')->primary();
            $table->string('form_id');
            $table->foreign('form_id')->references('form_id')->on('forms')->onDelete('cascade');
            $table->string('field_label');
            $table->string('field_type');
            $table->string('field_header');
            $table->json('more_options')->nullable();
            $table->boolean('isRequired');
            $table->text('field_placeholder')->nullable();
            $table->text('field_instructions')->nullable();
            $table->text('value')->nullable();
            $table->integer('field_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
