<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\Reportable;
use App\Enums\EstadoReporte;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('usuario_reporta_id')
                    ->nullable()
                    ->constrained('usuarios', 'id')
                    ->nullOnDelete();
            
            // Relación polimórfica. Apunta a tablas usuarios, comentarios y publicaciones
            $table->uuid('reportable_id');

            $table->enum('reportable_tipo', array_column(Reportable::cases(), 'value'));
            
            $table->text('motivo'); 

            $table->enum('estado', array_column(EstadoReporte::cases(), 'value')) 
                    ->default(EstadoReporte::PENDIENTE->value);

            $table->timestamp('created_at')->useCurrent();            

            $table->foreignUuid('resuelto_por')
                    ->nullable()
                    ->constrained('usuarios', 'id')
                    ->nullOnDelete();

            // Evita que el mismo usuario reporte dos veces lo mismo
            $table->unique(['usuario_reporta_id', 'reportable_tipo', 'reportable_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
