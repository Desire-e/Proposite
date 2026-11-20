<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Oposicion;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UsuarioSeeder extends Seeder
{
    private const AUXILIAR = 'Auxiliar Administrativo del Estado';
    private const POLICIA = 'Policía Nacional (Escala Básica)';

    /**
     * Crea el administrador, 4 opositores fijos (2 por oposición) y 10 opositores
     * aleatorios. Correo: [nombre]@gmail.com.
     *
     * No usa Faker: en producción se instala con "composer install --no-dev"
     * y Faker no estaría disponible.
     */
    public function run(): void
    {
        $oposiciones = Oposicion::whereIn('nombre', [self::AUXILIAR, self::POLICIA])
            ->pluck('id', 'nombre');

        if ($oposiciones->count() < 2) {
            throw new RuntimeException('Faltan oposiciones. Ejecuta antes OposicionSeeder.');
        }

        // Administrador: sin oposición; elige la comunidad que modera al iniciar sesión
        $this->crearUsuario('admin', 'admin@gmail.com', 'admin1234', Rol::ADMIN, null);

        // Opositores fijos: nombre => [oposición, contraseña]
        $fijos = [
            'alba'      => [self::AUXILIAR, 'albaalba'],
            'marcelino' => [self::AUXILIAR, 'marcemarce'],
            'emilio'    => [self::POLICIA, 'emilioemilio'],
            'petra'     => [self::POLICIA, 'petrapetra'],
        ];

        foreach ($fijos as $nombre => [$oposicion, $password]) {
            $this->crearUsuario($nombre, "{$nombre}@gmail.com", $password, Rol::OPOSITOR, $oposiciones[$oposicion]);
        }

        // Opositores aleatorios: solo la primera vez, para no sumar 10 más en cada ejecución
        $opositores = Usuario::where('rol', Rol::OPOSITOR)->count();

        if ($opositores >= count($fijos) + 10) {
            $this->command?->info('Ya existen los opositores aleatorios: no se crean más.');
            return;
        }

        $nombres = collect([
            'lucia', 'carmen', 'javier', 'sergio', 'nuria', 'raul', 'ines', 'diego', 'laura', 'pablo',
            'irene', 'hugo', 'sara', 'ivan', 'marta', 'adrian', 'elena', 'oscar', 'clara', 'ruben',
        ])->shuffle()->take(10);

        foreach ($nombres as $nombre) {
            // Contraseña: el nombre repetido (lucia => lucialucia)
            $this->crearUsuario($nombre, "{$nombre}@gmail.com", $nombre . $nombre, Rol::OPOSITOR, $oposiciones->random());
        }

        $this->command?->info('Seeder de usuarios completado.');
    }

    /**
     * firstOrCreate por correo: relanzar el seeder no duplica usuarios.
     * rol se asigna con forceFill para no depender de $fillable (BUG-06).
     */
    private function crearUsuario(string $nombre, string $email, string $password, Rol $rol, ?string $oposicionId): void
    {
        if (Usuario::withTrashed()->where('email', $email)->exists()) {
            return;
        }

        (new Usuario)->forceFill([
            'name'         => $nombre,
            'email'        => $email,
            'password'     => Hash::make($password),
            'rol'          => $rol,
            'oposicion_id' => $oposicionId,
        ])->save();
    }
}
