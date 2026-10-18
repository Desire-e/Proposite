<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\Usuario;
use App\Models\Oposicion;
use App\Models\Tema;
use App\Models\IntentoTest;
use App\Models\Publicacion;
use App\Models\Comentario;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class PerfilController extends Controller
{
    /**
     * Carga los datos del usuario y renderiza perfil
     */
    public function index(): Response 
    {
        $user = Auth::user();

        $oposicion = $user->oposicion;

        // Los intentos se calculan una vez y se reutilizan en 3 estadísticas
        $ultimosIntentos = $this->ultimosIntentos($user);


        return Inertia::render('opositor/perfil', [
            'user' => [
                'name' => $user->name,
                'oposicion' => $oposicion,
                'biografia' => $user->biografia ?? null,
                'signed_up_at' => $user->created_at,
                'anio_convocatoria' => $user->fecha_convocatoria
                    ? date("Y", strtotime($user->fecha_convocatoria))
                    : null,

                // ['total_temas', 'temas_estudiados', 'porcentaje']
                'progreso' => $this->progresoTemario($user), 
                // ['tests_realizados', 'tests_aprobados', 'nota_media']
                'estadisticas' => $this->estadisticasTests($ultimosIntentos),
                // ['titulo', 'nota_media']
                'tema_mas_fuerte' => $this->temaMasFuerte($ultimosIntentos),
                // [ ['id', 'tipo', 'texto', 'created_at'], [ ... ] ]
                'actividad_reciente' => $this->actividadReciente($user),
            ],
        ]);
    }
    

    /************************
     * FUNCIONES AUXILIARES DE INDEX
     *************************/

    /**
     * @param Usuario 
     * @return array de Temas estudiados, porcentaje de temas estudiados,
     * total de temas de la oposición,
     */
    private function progresoTemario(Usuario $user): array
    {
        $totalTemas = Tema::where('oposicion_id', $user->oposicion_id)->count();

        // Temas de esa oposición marcados como estudiados.
        $temasEstudiados = $user->checkedTemario() // obtiene temarios estudiados
            ->where('temarios.oposicion_id', $user->oposicion_id) // solo temas de su oposición
            ->count();
            
        $porcentaje = $totalTemas > 0 ? 
            (int) round($temasEstudiados / $totalTemas * 100) : 
            0;

        return [
            'total_temas' => $totalTemas,
            'temas_estudiados' => $temasEstudiados,
            'porcentaje' => $porcentaje,
        ];
    }

    /**
     * @param Usuario 
     * @return Collection del último intento de cada test que ha 
     * hecho el usuario
     */
    private function ultimosIntentos(Usuario $user): Collection
    {
        $ultimosIntentosTest = $user->intentosTests()
            // carga el test al que pertenece cada intento, y el temario asociado a ese test.    
            ->with('test.temario')
            // ordena intento de nuevo a viejo
            ->orderByDesc('created_at')->get() 
            // quita intentos en tests eliminados
            ->filter(fn ($intento) => $intento->test !== null)
            // se queda con el primero de cada test, el más nuevo
            ->unique('test_id');

        return $ultimosIntentosTest;  
    }

    /**
     * @param Collection de ultimosIntentos()
     * @return array de Tests realizados, tests aprobados, y nota media 
     * de todos los tests
     */
    private function estadisticasTests(Collection $ultimosIntentos): array
    {
        // cuenta cuantos tests hizo, contando solo el último intento de cada test
        $realizados = $ultimosIntentos->count();

        // contar cuantos fueron aprobados        
        $testsAprobados = $ultimosIntentos
            ->filter(fn ($intento) => (float) $intento->nota >= 5)
            ->count();
        
        // media de notas contando solo los ultimos intentos de cada test
        $notaMedia = $realizados > 0 
            ? round($ultimosIntentos->avg('nota'), 2)
            : 0;
        
        return [
            'tests_realizados' => $realizados,
            'tests_aprobados' => $testsAprobados,
            'nota_media' => $notaMedia,
        ];
    }

    /**
     * @param Collection de ultimosIntentos()
     * @return array de tema con mejor nota media 
     * @return null si no hay datos
     */
    private function temaMasFuerte(Collection $ultimosIntentos): ?array
    {
        $mejorNotaTema = $ultimosIntentos
            // quita intentos de tests asociados a temas eliminados 
            ->filter(fn ($intento) => $intento->test->temario !== null)
            // agrupa los intentos por temario
            ->groupBy(fn ($intento) => $intento->test->temario_id)
            // transforma cada grupo de intentos por tema, en un 
            // resumen del tema, generando un nuevo elemento con 3 datos
            ->map(fn ($grupo) => [ // intentos de un mismo tema
                // 'numero_tema' => 
                    // $grupo->first()->test->temario->numero_tema, // primer intento de ese grupo, su numero
                'titulo' => 
                    $grupo->first()->test->temario->titulo, // primer intento de ese grupo, su titulo
                'nota_media' => 
                    round($grupo->avg('nota'), 2), // primer intento de ese grupo, su nota media
            ])
            ->sortByDesc('nota_media') // ordenar de mayor a menor nota media
            ->first(); // mejor nota
    
        return $mejorNotaTema;
    }

    /**
     * @param Usuario
     * @return Collection de las 2 últimas publicaciones 
     * o comentarios del usuario
     */
    private function actividadReciente(Usuario $user): Collection
    {
        $publicaciones = $user->publicaciones()
            ->latest()->take(2)->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'tipo' => 'publicacion',
                'texto' => $p->titulo,
                'created_at' => $p->created_at,
            ]);

        $comentarios = $user->comentarios()
            ->latest()->take(2)->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'tipo' => 'comentario',
                'texto' => $c->contenido,
                'created_at' => $c->created_at,
            ]);

        return $publicaciones->concat($comentarios)
            ->sortByDesc('created_at')->take(2)->values();
    }




    /**
     * Actualiza los datos del usuario
     */

    public function edit(): Response 
    {
        $user = Auth::user();

        return Inertia::render('opositor/perfil-edit', [
            'user' => [
                'name' => $user->name ?? null,
                'email'=> $user->email ?? null,
                'biografia'=> $user->biografia ?? null,
                'fecha_convocatoria'=> $user->fecha_convocatoria ?? null,
            ],
        ]);

    }


    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:150', 
                Rule::unique('usuarios', 'name') // nombre único
                    ->ignore($request->user()->id), // excluye al usuario autenticado
            ],
            'biografia' => ['nullable', 'string'],
            'fecha_convocatoria' => ['nullable', 'date', 'after:today'], 
        ], [
            'fecha_convocatoria.after' => 'La fecha de convocatoria debe ser posterior a hoy.',
        ]);

        $user = $request->user(); // usuario autenticado que hizo la request

        $user->name = $validated['name'];
        $user->biografia = $validated['biografia'] ?? null;
        $user->fecha_convocatoria = $validated['fecha_convocatoria'] ?? null;
        
        $user->save();

        return to_route('opositor.perfil')->with('success', 'Perfil actualizado correctamente.');
    }

}
