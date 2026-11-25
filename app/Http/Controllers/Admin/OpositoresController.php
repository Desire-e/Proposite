<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Usuario;

use App\Enums\Rol;

use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class OpositoresController extends Controller
{
    /**
     * Muestra el listado de opositores de la comunidad, baneados o no
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Usuario::class);

        $oposicion = auth()->user()->comunidad();

        $opositores = Usuario::withTrashed()
            ->with(['baneadoPor' => fn ($q) => $q->withTrashed()]) // incluso si el admin fue eliminado, aparece su nombre
            ->where('oposicion_id', $oposicion?->id)
            ->orderBy('name')
            ->get();
        
        $listado_opositores = $opositores->map(fn ($us)=> [
            'id' => $us->id,
            'name' => $us->name,
            'email' => $us->email,
            'created_at' => $us->created_at,
            'baneado' => $us->deleted_at === null 
                ? null 
                : [ 
                    'deleted_at' => $us->deleted_at,
                    'deleted_by' => $us->baneadoPor?->name,
                ]
        ]);

        $listado_admins = Usuario::where('rol', Rol::ADMIN->value)->pluck('name');

        return Inertia::render('admin/usuarios/index', [
            'oposicion' => $oposicion?->nombre,
            'opositores' => $listado_opositores,
            'administradores' => $listado_admins
        ]);
    }


    /**
     * Revocar baneo de un usuario
     */
    public function recover(Usuario $usuario): RedirectResponse 
    {
        Gate::authorize('recover', $usuario);

        $usuario->deleted_by = null;
        
        // limpia quién baneó (deleted_by), sin disparar eventos
        $usuario->saveQuietly(); 
        
        // guarda deleted_by y deleted_at a la vez, y dispara evento 
        // restored (recupera publicaciones, comentarios e intentos)    
        $usuario->restore();    

        return back();
    }

    public function ban(Request $request, Usuario $usuario): RedirectResponse 
    {
        Gate::authorize('delete', $usuario);

        $usuario->deleted_by = $request->user()->id;
        $usuario->save();
        $usuario->delete();
        
        return back();
    }
}
