<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tema;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ToggleTemaRequest;


class TemariosController extends Controller
{
    /**
     * Carga los datos del temario y renderiza
     */
    public function index(Request $request): Response 
    {
        $user = $request->user();
        $oposicion = $user->oposicion;

        // Temas marcados como estudiados
        $checked = $user->checkedTemario() 
            // guardar el estado de cada tema en una colección indexada por el ID del tema. [ '1'=>true, '1.1.'=> false, ... ]
            ->pluck('checks_temario.checked', 'temarios.id');

        // Temario, con recursos y estados marcados de cada uno 
        $temario = $oposicion->temario()
            // se carga también los recursos relacionados con cada tema
            ->with('recursos') 
            ->get()
            // comparar textos siguiendo un orden natural
            ->sortBy('numero_tema', SORT_NATURAL)
            // se reinician los índices del array para que quede ordenado también
            ->values()
            ->map(fn (Tema $tema) => [
            'id' => $tema->id,
            'bloque' => $tema->bloque,
            'numero_tema' => $tema->numero_tema,
            'titulo' => $tema->titulo,
            'recursos' => $tema->recursos ?? null,
            'checked' => (bool) ($checked[$tema->id] ?? false), // devuelve el estado guardado o false si no existe
        ]);

        return Inertia::render('temario/index', [
            'temario' => $temario, 
            'oposicion' => $oposicion,
        ]);
    }


    /**
     * Cambiar checklist
     */
    public function toggleCheck(ToggleTemaRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Qué tema es y cambio de estado pedido 
        $temaId = $request->validated()['tema'];
        $checked =  $request->boolean('checked');

        $existeFila = $user->checkedTemario()->where('temarios.id', $temaId)->exists();

        // actualiza registro anterior
        if($existeFila){
            $user->checkedTemario()->updateExistingPivot($temaId, ['checked'=>$checked]);
        }
        // inserta nuevo registro
        else {
            $user->checkedTemario()->attach($temaId, ['checked'=>$checked]);
        }

        // vuelve a la misma página y React recibe el temario actualizado.
        return back();
    }
}
