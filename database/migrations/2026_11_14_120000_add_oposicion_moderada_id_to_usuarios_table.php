<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Oposición que está moderando un administrador (RF2b).
     * Es independiente de oposicion_id para que el admin no cuente
     * como miembro de la oposición en recuentos, ranking, etc.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignUuid('oposicion_moderada_id')
                ->nullable()
                ->after('oposicion_id')
                ->constrained('oposiciones', 'id')
                ->nullOnDelete(); // si se elimina la oposición, el admin vuelve a elegir
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['oposicion_moderada_id']);
            $table->dropColumn('oposicion_moderada_id');
        });
    }
};
