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
        if(!$usuario->esAdmin()) {
            return match ($ability) {
                'create', 'update', 'delete' => 
                    Response::deny('Solo los administradores pueden realizar esta acción.'),
                default => null,
            };
        }

        return null;
    }


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
        return Response::allow();
    }

    public function update(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        if (!$this->visibleParaUsuario($usuario, $model)) {
            return Response::denyAsNotFound();
        }

        return $model instanceof Test && $model->intentosTest()->exists()
            ? Response::deny('No se puede editar un test que ya han realizado usuarios.')
            : Response::allow();
    }

    public function delete(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    {
        return $this->visibleParaUsuario($usuario, $model)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    // public function restore(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    // {
    //     return Response::deny('Solo los administradores pueden realizar esta acción.');
    // }

    // public function forceDelete(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): Response
    // {
    //     return Response::deny('Solo los administradores pueden realizar esta acción.');
    // }


    private function visibleParaUsuario(Usuario $usuario, Test|OpcionRespuesta|Pregunta $model): bool
    {
        $oposicionId = $usuario->comunidad()->id;
        $admin = $usuario->esAdmin();

        if ($model instanceof Test) {
            $mismaOposicion = $oposicionId === $model->temario->oposicion_id;
        }
        else if ($model instanceof Pregunta) {
            $mismaOposicion = $oposicionId === $model->test->temario->oposicion_id;
        }
        else {
            $mismaOposicion = $oposicionId === $model->pregunta->test->temario->oposicion_id; 
        }

        return $admin 
            ?  $mismaOposicion
            : !$model->trashed() && $mismaOposicion;
    }
}
