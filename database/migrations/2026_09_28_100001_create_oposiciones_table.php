<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * php artisan migrate
     */
    public function up(): void {
        Schema::create('oposiciones', function (Blueprint $table) {
            
            $table->uuid('id')->primary();

            $table->string('nombre', length:200);
            
            $table->text('descripcion');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('oposiciones');
    }
};
