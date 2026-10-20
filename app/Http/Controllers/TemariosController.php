<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Collection;
// use Illuminate\Validation\Rule;

use App\Models\Tema;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class TemariosController extends Controller
{
    /**
     * Carga los datos del temario y renderiza
     */

    public function index(): Response 
    {
        $oposicion = Auth::user()->oposicion;

        $temario = $oposicion->temario()
            // se carga también los recursos relacionados con cada tema
            ->with('recursos') 
            ->get()
            ->sort(fn (Tema $a, Tema $b) =>
                    // comparar textos siguiendo un orden natural
                    strnatcmp($a->numero_tema, $b->numero_tema)
            )
            // se reinician los índices del array para que quede ordenado también
            ->values();

        // Temarios marcados como estudiados
        $checkedTemario = Auth::user()->checkedTemario() 
            // guardar el estado de cada tema en una colección indexada por el ID del tema. [ '1'=>true, '1.1.'=> false, ... ]
            ->pluck('checks_temario.checked', 'temarios.id');

        // Temario, con recursos y estados marcados de cada uno 
        $temario = $temario->map(fn (Tema $tema) => [
            'id' => $tema->id,
            'bloque' => $tema->bloque,
            'numero_tema' => $tema->numero_tema,
            'titulo' => $tema->titulo,
            'recursos' => $tema->recursos ?? null,
            'checked' => (bool) ($checkedTemario[$tema->id] ?? false), // devuelve el estado guardado o false si no existe
        ]);

        return Inertia::render('temario', [
            'temario' => $temario, 
            'oposicion' => $oposicion,
        ]);
    }


    /**
     * Cambiar checklist
     */
    
    public function toggleCheck(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'checked' => ['required', 'boolean'],
            'tema' => [
                'required',
                // el tema debe existir y ser de la oposición del usuario
                Rule::exists('temarios', 'id')
                    ->where('oposicion_id', $user->oposicion_id),
            ],
        ]);

        DB::table('checks_temario')->updateOrInsert(
            // condiciones para buscar la fila en tabla checks_temario 
            ['usuario_id' => $user->id, 'temario_id' => $validated['tema']],
            // valores a actualizar/insertar
            ['checked' => $validated['checked']],
        );
        
        // vuelve a la misma página y React recibe el temario actualizado.
        return back();
    }


}
