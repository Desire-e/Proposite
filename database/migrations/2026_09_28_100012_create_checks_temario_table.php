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
        Schema::create('checks_temario', function (Blueprint $table) {
            
            $table->foreignUuid('usuario_id')
                  ->constrained('usuarios')
                  ->cascadeOnDelete();

            $table->foreignUuid('temario_id')
                  ->constrained('temarios', 'id')
                  ->cascadeOnDelete();

            $table->boolean('checked')->default(false);

            $table->primary(['usuario_id', 'temario_id']); // PK compuesta

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks_temario');
    }
};
