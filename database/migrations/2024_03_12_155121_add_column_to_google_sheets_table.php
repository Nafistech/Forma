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
        Schema::table('google_sheets', function (Blueprint $table) {
            $table->string('form_id')->nullable();
            $table->foreign('form_id')->references('form_id')->on('forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_sheets', function (Blueprint $table) {
            //
        });
    }
};
