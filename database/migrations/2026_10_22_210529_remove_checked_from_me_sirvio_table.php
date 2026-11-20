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
        // Las filas con checked = false equivalen a "sin me sirvió": se eliminan.
        DB::table('me_sirvio')->where('checked', false)->delete();

        Schema::table('me_sirvio', function (Blueprint $table) {
            $table->dropColumn('checked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('me_sirvio', function (Blueprint $table) {
            // Las filas que quedan eran las marcadas, así que vuelven como true.
            $table->boolean('checked')->default(true);
        });
    }
};
