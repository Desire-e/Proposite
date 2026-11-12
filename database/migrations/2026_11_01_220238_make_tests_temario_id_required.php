<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar la FK actual (tiene ON DELETE SET NULL)
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['temario_id']);
        });

        // Cambiar la columna a NOT NULL
        Schema::table('tests', function (Blueprint $table) {
            $table->foreignUuid('temario_id')->nullable(false)->change();
        });

        // Recrear la FK con cascadeOnDelete
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('temario_id')
                ->references('id')->on('temarios')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['temario_id']);
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->foreignUuid('temario_id')->nullable()->change();
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('temario_id')
                ->references('id')->on('temarios')
                ->nullOnDelete();
        });
    }
};