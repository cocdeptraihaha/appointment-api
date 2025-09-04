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
        Schema::create('appointments', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('title', 200);
            $table->string('type_id', 10)->nullable();
            $table->string('contact_id', 10)->nullable();
            $table->string('staff_id', 10)->nullable();
            $table->bigInteger('start_time');
            $table->bigInteger('end_time');
            
            $table->foreign('type_id')->references('id')->on('appointment_types');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('staff_id')->references('id')->on('staff');
            
            $table->index('start_time');
            $table->index('end_time');
            $table->index('type_id');
            $table->index('contact_id');
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
