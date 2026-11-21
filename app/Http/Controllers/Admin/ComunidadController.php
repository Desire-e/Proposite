<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SelectOposicionRequest;
use App\Services\Admin\PanelService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class ComunidadController extends Controller
{
    public function __construct(private PanelService $panel) {}


    /**
     * Panel de selección de oposición a moderar
     */
    public function index(Request $request): Response 
    {
        return Inertia::render('admin/welcome', [
            'comunidades'=> $this->panel->comunidadesDisponibles(),
        ]);
    }
    

    /**
     * Guarda la oposición elegida y entra al panel 
     * administrativo de su comunidad
     */
    public function select(SelectOposicionRequest $request): RedirectResponse
    {
        // Añadir oposicion_moderada_id a $fillable de Usuario abre la puerta 
        // a que create() y update() también lo acepten. 
        $admin = $request->user();
        $admin->oposicion_moderada_id = $request->validated()['oposicion_id'];
        $admin->save();

        return to_route('admin.panel');
    }
}
