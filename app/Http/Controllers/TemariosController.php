<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tema;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ToggleTemaRequest;

use App\Services\TemarioService;


class TemariosController extends Controller
{
    public function __construct(private TemarioService $temario) {}


    /**
     * Carga los datos del temario y renderiza
     */
    public function index(Request $request): Response 
    {
        $user = $request->user();

        $oposicion = $user->comunidad();

        return Inertia::render('temario/index', [
            'temario' => $this->temario->temarioData($oposicion, $user), 
            'oposicion' => $oposicion->only('id', 'nombre'),
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

        // Comprueba sobre todas las filas del pivote, no solo las marcadas
        $existeFila = $user->checkTemario()->where('temario_id', $temaId)->exists();


        // si existe actualiza registro anterior; si no, inserta registro nuevo
        if($existeFila){
            $user->checkTemario()->updateExistingPivot($temaId, ['checked' => $checked]);
        }
        else {
            $user->checkTemario()->attach($temaId, ['checked' => $checked]);
        }

        return back();
    }
}
