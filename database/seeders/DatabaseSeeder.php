<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Ejecutar los seeders:
 * php artisan db:seed            (en producción: php artisan db:seed --force)
 *
 * Uno concreto:
 * php artisan db:seed --class=UsuarioSeeder
 *
 * Requiere ADMIN_PASSWORD y DEMO_PASSWORD en el .env (ver config/demo.php).
 */

// Documentación: https://laravel.com/docs/12.x/seeding#writing-seeders

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * El orden importa: cada seeder usa los datos de los anteriores.
     */
    public function run(): void {

        $this->call([
            // Datos oficiales (BOE)
            OposicionSeeder::class,
            TemarioAuxiliarAdministrativoSeeder::class,
            TemarioPoliciaNacionalSeeder::class,
            RecursoSeeder::class,

            // Usuarios: administrador, cuentas de demo y opositores aleatorios
            UsuarioSeeder::class,

            // Comunidad: publicaciones con sus comentarios, y «me sirvió»
            PublicacionSeeder::class,
            MeSirvioSeeder::class,

            // Estudio: tests, intentos y temas marcados
            TestsSeeder::class,
            IntentosTestSeeder::class,
            CheckTemarioSeeder::class,

            // Moderación: reportes y un baneado por oposición
            ReporteSeeder::class,
            BaneoSeeder::class,

            // Progreso garantizado de las cuentas de demo (al final, sobre todo lo anterior)
            CuentasDemoSeeder::class,
        ]);

    }
}
