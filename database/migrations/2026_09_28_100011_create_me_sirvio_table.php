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
        Schema::create('me_sirvio', function (Blueprint $table) {
            $table->foreignUuid('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignUuid('publicacion_id')
                  ->constrained('publicaciones', 'id')
                  ->cascadeOnDelete();

            $table->boolean('checked')->default(false);

            $table->primary(['usuario_id', 'publicacion_id']); // PK compuesta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('me_sirvio');
    }
};
