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
        Schema::create('intentos_test', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('usuario_id')
                    ->constrained('usuarios','id')
                    ->cascadeOnDelete();

            $table->foreignUuid('test_id')
                    ->nullable()
                    ->constrained('tests','id')
                    ->nullOnDelete();
            
            // 5 cifras en total, 3 decimales 
            $table->decimal('nota', total: 5, places: 3)->unsigned();
            
            $table->timestamp('created_at')->useCurrent();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intentos_test');
    }
};
