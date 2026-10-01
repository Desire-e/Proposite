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
        Schema::create('recursos', function (Blueprint $table) {
            
            $table->uuid('id')->primary();

            $table->foreignUuid('subido_por')
                    ->nullable()
                    ->constrained('usuarios', 'id')
                    ->nullOnDelete();
            
            $table->foreignUuid('temario_id')
                    ->constrained('temarios', 'id')
                    ->cascadeOnDelete();
            
            $table->string('titulo', length: 250);
            
            $table->text('recurso_url');
            
            $table->timestamp('created_at')->useCurrent();
            
            $table->softDeletes(); // deleted_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recursos');
    }
};
