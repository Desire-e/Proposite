<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Rol;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->timestamp('email_verified_at')->nullable();
        //     $table->string('password');
        //     $table->rememberToken();
        //     $table->timestamps();
        // });
        Schema::create('usuarios', function (Blueprint $table) {

            $table->uuid('id')->primary();
            
            $table->foreignUuid('oposicion_id')
                    ->nullable()
                    ->constrained('oposiciones','id')
                    ->nullOnDelete();
            
            $table->string('name', length: 150)
                    ->unique();
            
            $table->string('email', length: 150)
                    ->unique();
            
            $table->string('password', length: 250);

            /**
             * Enums.
             * 
             * Rol::cases() Devuelve un array de objetos Rol [Rol::ADMIN, ...]
             * Cada objeto Rol contiene ->name, ->value
             * array_column() obtiene la propiedad concreta de cada elemento de un array
             */            
            $table->enum('rol', array_column(Rol::cases(), 'value'))
                    ->default(Rol::OPOSITOR->value);
            
            $table->date('fecha_convocatoria')
                    ->nullable(); // un usuario puede que todavía no haya seleccionado/registrado una convocatoria.
            
            $table->text('biografia')
                    ->nullable();

            $table->boolean('suspendido')
                    ->default(false);
            
            $table->foreignUuid('suspendido_por')
                    ->nullable()
                    ->constrained('usuarios','id')
                    ->nullOnDelete();

            $table->foreignUuid('avatar_id')
                    ->nullable()
                    ->constrained('avatares','id')
                    ->nullOnDelete();

            $table->timestamps(); // created_at, updated_at
        });

        // Schema::create('password_reset_tokens', function (Blueprint $table) {
        //     $table->string('email')->primary();
        //     $table->string('token');
        //     $table->timestamp('created_at')->nullable();
        // });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // $table->foreignId('user_id')->nullable()->index();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('users');
        Schema::dropIfExists('usuarios');
        // Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
