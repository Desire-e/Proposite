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

    public function before(Usuario $usuario, string $ability): Response|null
    {
        return $usuario->esAdmin() ? Response::allow() : null;
    }


    public function view(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        return $this->visibleParaOpositor($usuario, $model)
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


    private function visibleParaOpositor(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): bool
    {
        if ($model instanceof Test) {
            $test = $model;
            return !$test->trashed()
                && $usuario->oposicion_id === $test->temario->oposicion_id;
        }
        else if ($model instanceof Pregunta) {
            $pregunta = $model;
            return !$pregunta->trashed()
                && $usuario->oposicion_id === $pregunta->test->temario->oposicion_id;
        }
        else {
            $opcion = $model;
            return !$opcion->trashed()
                && $usuario->oposicion_id === $opcion->pregunta->test->temario->oposicion_id;
        }
    }
}
