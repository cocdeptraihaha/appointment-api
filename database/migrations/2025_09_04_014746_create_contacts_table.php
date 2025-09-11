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
        Schema::create('contacts', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->text('avatar')->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone_number', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
