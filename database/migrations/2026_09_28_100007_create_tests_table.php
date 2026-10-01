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
        Schema::create('tests', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('creado_por')
                ->nullable()
                ->constrained('usuarios', 'id')
                ->nullOnDelete();

            $table->foreignUuid('temario_id')
                ->nullable()
                ->constrained('temarios', 'id')
                ->nullOnDelete();

            $table->string('titulo', length:200);

            $table->timestamp('created_at')->useCurrent();
            
            $table->softDeletes(); // deleted_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
