<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// Archivo: database/migrations/2025_05_14_153534_create_categories_table.php
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Esto crea una columna de tipo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
