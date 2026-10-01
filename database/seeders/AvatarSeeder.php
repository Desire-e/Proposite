<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Avatar;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Un seeder en Laravel es una clase utilizada para poblar automáticamente la base de datos 
 * con datos de prueba o configuraciones iniciales.  
 * 
 * Contienen un método run() donde se define la lógica para insertar registros mediante 
 * el query builder o Eloquent factories. 
 */

class AvatarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {

        $avatares = [
            ['nombre' => 'Avatar 1', 'ruta_archivo' => 'avatares/avatar-mujer-1.png'],
            ['nombre' => 'Avatar 2', 'ruta_archivo' => 'avatares/avatar-mujer-2.png'],
            ['nombre' => 'Avatar 3', 'ruta_archivo' => 'avatares/avatar-hombre-1.png'],
            ['nombre' => 'Avatar 4', 'ruta_archivo' => 'avatares/avatar-hombre-2.png'],
        ];

        // Avatar::insert($avatares);

        foreach ($avatares as $avatar) {
            Avatar::insert([
                'id' => (string) Str::uuid(),
                'nombre' => $avatar['nombre'],
                'ruta_archivo' => $avatar['ruta_archivo'],
            ]);
        }

    }
}
