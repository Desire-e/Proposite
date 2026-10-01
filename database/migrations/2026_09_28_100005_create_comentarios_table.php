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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('usuario_id')
                    ->constrained('usuarios','id')
                    ->cascadeOnDelete();
            
            $table->foreignUuid('publicacion_id')
                    ->constrained('publicaciones','id')
                    ->cascadeOnDelete();
            
            $table->text('contenido');
            
            $table->timestamp('created_at')->useCurrent();
            
            $table->softDeletes(); // deleted_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
