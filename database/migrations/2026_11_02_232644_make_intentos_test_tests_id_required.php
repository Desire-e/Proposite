<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Elimina la foreign key actual (nullOnDelete)
        Schema::table('intentos_test', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
        });

        // Elimina intentos huérfanos, si los hay
        DB::table('intentos_test')->whereNull('test_id')->delete();

        // Cambia la columna a NOT NULL
        Schema::table('intentos_test', function (Blueprint $table) {
            $table->foreignUuid('test_id')->nullable(false)->change();
        });

        // Recrea la foreign key con cascade
        Schema::table('intentos_test', function (Blueprint $table) {
            $table->foreign('test_id')
                ->references('id')->on('tests')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('intentos_test', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
            $table->foreignUuid('test_id')->nullable()->change();
            $table->foreign('test_id')
                ->references('id')->on('tests')
                ->nullOnDelete();
        });
    }
};
