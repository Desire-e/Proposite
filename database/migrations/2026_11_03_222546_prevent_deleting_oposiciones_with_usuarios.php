<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Borrado físico: la FK impide borrar una oposición con usuarios
        // (antes era nullOnDelete: dejaba a los usuarios sin oposición)
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['oposicion_id']);
            
            $table->foreign('oposicion_id')
                ->references('id')->on('oposiciones')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['oposicion_id']);
            $table->foreign('oposicion_id')
                ->references('id')->on('oposiciones')
                ->nullOnDelete();
        });
    }
};