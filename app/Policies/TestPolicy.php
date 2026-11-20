<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\Pregunta;
use App\Models\OpcionRespuesta;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Sirve para los modelos: 
 * Test, Pregunta y OpcionRespuesta
 */

class TestPolicy
{

    public function view(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        return $this->visibleParaUsuario($usuario, $model)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    
    /**
     * Solo administrador
     */
    public function create(Usuario $usuario): Response
    {
        return Response::deny('Solo los administradores pueden realizar esta acción.');
    }

    public function update(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        return Response::deny('Solo los administradores pueden realizar esta acción.');
    }

    public function delete(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        return Response::deny('Solo los administradores pueden realizar esta acción.');
    }

    public function restore(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        return Response::deny('Solo los administradores pueden realizar esta acción.');
    }

    public function forceDelete(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        return Response::deny('Solo los administradores pueden realizar esta acción.');
    }


    private function visibleParaUsuario(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): bool
    {
        $oposicionId = $usuario->comunidad()->id;

        if ($model instanceof Test) {
            $test = $model;
            return !$test->trashed()
                && $oposicionId === $test->temario->oposicion_id;
        }
        else if ($model instanceof Pregunta) {
            $pregunta = $model;
            return !$pregunta->trashed()
                && $oposicionId === $pregunta->test->temario->oposicion_id;
        }
        else {
            $opcion = $model;
            return !$opcion->trashed()
                && $oposicionId === $opcion->pregunta->test->temario->oposicion_id;
        }
    }
}
