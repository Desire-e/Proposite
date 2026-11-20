<?php

namespace App\Policies;

use App\Models\Oposicion;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class OposicionPolicy
{
    public function before(Usuario $usuario, string $ability): Response|bool|null
    {
        if (!$usuario->esAdmin()) { 
            return Response::deny('Solo los administradores pueden realizar esta acción.');
        }

        return null;
    }

    /**
     * Eliminar @param Oposicion
     * No permite borrar una oposición que tenga usuarios.
     * Evita que un opositor quede sin oposición y se disparen fallos.
     */
    public function delete(Usuario $usuario, Oposicion $oposicion): Response
    {
        if ($oposicion->trashed()) {
            return Response::deny('Esta oposición ya está eliminada.');
        }

        $numUsuarios = $oposicion->usuarios()->count();

        return $numUsuarios > 0
            ? Response::deny("La oposición no se puede eliminar: tiene {$numUsuarios} opositores inscritos.")
            : Response::allow();
    }


    /**
     * Crear @param Oposicion
     */

}
