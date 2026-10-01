<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('avatares', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('nombre', length: 50)->unique();
            
            // $table->binary('archivo');

            $table->string('ruta_archivo', 255);
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avatares');
    }
};
