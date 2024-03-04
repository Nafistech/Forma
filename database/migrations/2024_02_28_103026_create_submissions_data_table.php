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
        Schema::create('submission_data', function (Blueprint $table) {
            $table->id('submission_data_id');
            $table->foreignId('submission_id')->references('submission_id')->on('submissions')->onDelete('cascade');
            $table->string('field_id');
            $table->foreign('field_id')->references('field_id')->on('fields')->onDelete('cascade');
            $table->json('field_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions_data');
    }
};
