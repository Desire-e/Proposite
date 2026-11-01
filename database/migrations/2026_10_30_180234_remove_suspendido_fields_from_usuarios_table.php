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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['suspendido_por']);
            $table->dropColumn(['suspendido', 'suspendido_por']);

            $table->foreignUuid('deleted_by')
                    ->nullable()
                    ->after('deleted_at')
                    ->constrained('usuarios', 'id')
                    ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');

            $table->boolean('suspendido')->default(false);
            $table->foreignUuid('suspendido_por')
                    ->nullable()
                    ->constrained('usuarios', 'id')
                    ->nullOnDelete();
        });
    }
};
