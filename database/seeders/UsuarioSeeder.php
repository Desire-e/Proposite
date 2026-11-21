<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Oposicion;
use App\Models\Usuario;
use Database\Seeders\Concerns\FechasDemo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class UsuarioSeeder extends Seeder
{
    use FechasDemo;

    public const ADMIN_EMAIL = 'admin@example.com';

    // Opositores aleatorios por oposición, además de la cuenta de demo
    private const OPOSITORES_POR_OPOSICION = 14;

    /**
     * Crea el administrador, las cuentas de demo (una por oposición) y 14
     * opositores aleatorios por oposición.
     *
     * - Correos en example.com, un dominio reservado para ejemplos.
     * - Contraseñas desde el .env (config/demo.php), nunca en el código:
     *   ADMIN_PASSWORD para el administrador y DEMO_PASSWORD para los opositores.
     * - No usa Faker: en producción se instala con "composer install --no-dev".
     */
    public function run(): void
    {
        $adminPassword = config('demo.admin_password');
        $demoPassword = config('demo.demo_password');

        if (blank($adminPassword) || blank($demoPassword)) {
            throw new RuntimeException('Faltan ADMIN_PASSWORD y DEMO_PASSWORD en el .env (ver .env.example).');
        }

        $oposiciones = Oposicion::pluck('id', 'nombre');

        if ($oposiciones->isEmpty()) {
            throw new RuntimeException('Faltan oposiciones. Ejecuta antes OposicionSeeder.');
        }

        // Un solo hash para todos los opositores: misma contraseña, menos tiempo de bcrypt
        $hashOpositor = Hash::make($demoPassword);

        // Administrador: sin oposición inscrita ni comunidad moderada.
        // Al iniciar sesión elige la comunidad que modera.
        $this->crearUsuario([
            'name' => 'Administración Proposite',
            'email' => self::ADMIN_EMAIL,
            'password' => Hash::make($adminPassword),
            'rol' => Rol::ADMIN,
            'oposicion_id' => null,
            'created_at' => $this->inicioDemo()->subDay(),
        ]);

        // Cuentas de demo: las primeras en registrarse, para tener historial
        foreach (CuentasDemoSeeder::CUENTAS as $nombreOposicion => $cuenta) {
            if (!isset($oposiciones[$nombreOposicion])) {
                continue;
            }

            $this->crearUsuario([
                'name' => $cuenta['name'],
                'email' => $cuenta['email'],
                'password' => $hashOpositor,
                'rol' => Rol::OPOSITOR,
                'oposicion_id' => $oposiciones[$nombreOposicion],
                'biografia' => $cuenta['biografia'],
                'fecha_convocatoria' => now()->addMonths(6)->toDateString(),
                'created_at' => $this->fechaEntre($this->inicioDemo(), $this->inicioDemo()->addDays(2)),
            ]);
        }

        // Opositores aleatorios: oposición, biografía, convocatoria y fecha de alta al azar.
        // Si ya existen (por correo), no se vuelven a crear.
        $nombres = collect($this->nombres())->shuffle()->values();
        $biografias = collect($this->biografias())->shuffle();
        $porOposicion = $nombres->chunk(self::OPOSITORES_POR_OPOSICION)->values();

        foreach ($oposiciones->values() as $i => $oposicionId) {
            foreach ($porOposicion[$i] ?? [] as $nombre) {
                $this->crearUsuario([
                    'name' => $nombre,
                    'email' => Str::of(Str::ascii($nombre))->lower()->replace(' ', '.') . '@example.com',
                    'password' => $hashOpositor,
                    'rol' => Rol::OPOSITOR,
                    'oposicion_id' => $oposicionId,
                    // 6 de cada 10 escriben una biografía; la mitad indica su convocatoria
                    'biografia' => random_int(1, 10) <= 6 ? $biografias->pop() : null,
                    'fecha_convocatoria' => random_int(0, 1)
                        ? $this->fechaEntre(now()->addMonths(4), now()->addMonths(9))->toDateString()
                        : null,
                    // Alta con al menos dos semanas de margen para participar
                    'created_at' => $this->fechaEntre(null, now()->subDays(14)),
                ]);
            }
        }

        $this->command?->info('Seeder de usuarios completado: ' . Usuario::count() . ' usuarios.');
    }

    /**
     * Si el correo ya existe (incluso baneado), no lo crea: relanzar el seeder
     * no duplica usuarios. rol, created_at y updated_at no son asignables en
     * masa: se asignan con forceFill.
     */
    private function crearUsuario(array $datos): void
    {
        if (Usuario::withTrashed()->where('email', $datos['email'])->exists()) {
            return;
        }

        (new Usuario)->forceFill($datos + ['updated_at' => $datos['created_at']])->save();
    }

    /**
     * 28 nombres: 14 para cada oposición. Ninguno coincide con las cuentas de demo.
     */
    private function nombres(): array
    {
        return [
            'Alba Herrero', 'Marcelino Prieto', 'Emilio Vidal', 'Petra Lozano',
            'Carmen Delgado', 'Javier Molina', 'Sergio Castro', 'Nuria Sanz',
            'Raúl Iglesias', 'Inés Garrido', 'Diego Fuentes', 'Laura Cabrera',
            'Pablo Navarro', 'Irene Domínguez', 'Hugo Serrano', 'Sara Méndez',
            'Iván Rubio', 'Marta Gil', 'Adrián Moreno', 'Elena Pastor',
            'Óscar León', 'Clara Benítez', 'Rubén Cano', 'Noelia Vega',
            'Álvaro Santos', 'Beatriz Ramos', 'Tomás Herrera', 'Paula Calvo',
        ];
    }

    private function biografias(): array
    {
        return [
            'Estudio por las tardes después del trabajo. Mi objetivo es aprobar en la próxima convocatoria.',
            'Segunda vez que me presento. Esta vez con más constancia y haciendo test todas las semanas.',
            'Acabo de terminar mis estudios y preparo mi primera oposición. Cualquier consejo me viene bien.',
            'Con dos peques en casa, estudio a primera hora de la mañana. Me organizo por bloques.',
            'Me gusta hacer esquemas y compartirlos. Si te sirven, pídemelos.',
            'Preparo la oposición por libre, sin academia. Aquí busco compañeros con los que repasar.',
            'Combino el estudio con el entrenamiento físico. Voy tema a tema, sin prisa pero sin pausa.',
            'Trabajé años en la empresa privada y ahora quiero dar el salto a lo público.',
            'Me cuesta la parte de legislación, pero los test me ayudan mucho a fijarla.',
            'Estudio con tarjetas de memoria y repaso espaciado. Me gusta compartir métodos.',
            'Vengo de otra oposición, así que parte del temario ya me suena.',
            'Objetivo: un tema por semana y un simulacro cada domingo.',
            'Me apunté a Proposite para no estudiar en soledad. ¡Ánimo a todos!',
            'Estudio en la biblioteca de mi barrio de lunes a viernes.',
            'Repaso en el transporte público con los test del móvil.',
            'Tercera convocatoria. Ya no me rindo.',
            'Prefiero entender antes que memorizar; luego los test lo afianzan.',
            'Buscando grupo de estudio para la parte de derecho constitucional.',
        ];
    }
}
