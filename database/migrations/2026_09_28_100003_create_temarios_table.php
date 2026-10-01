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
        Schema::create('temarios', function (Blueprint $table) {
            
            $table->uuid('id')->primary();

            $table->foreignUuid('oposicion_id')
                    ->constrained('oposiciones','id')
                    ->cascadeOnDelete();
            
            $table->string('numero_tema', length:10);
            
            $table->string('titulo', length:250);

            // 1 misma oposicion no puede 2 temas con mismo numero (índice)
            $table->unique(['oposicion_id', 'numero_tema']); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temarios');
    }
};
