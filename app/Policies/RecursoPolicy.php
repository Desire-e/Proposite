<?php

namespace App\Policies;

use App\Models\Recurso;
use App\Models\Tema;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;


class RecursoPolicy
{
    public function before(Usuario $usuario, string $ability): Response|null
    {
        // No admin
        if (!$usuario->esAdmin()) { 
            return Response::deny('Solo los administradores pueden realizar esta acción'); 
        }

        return null;
    }

    /**
     * Crear Recurso de un @param Tema
     */
    public function create(Usuario $usuario, Tema $tema): Response
    {
        return $this->moderaTema($usuario, $tema);
    }

    /**
     * Eliminar @param Recurso
     */
    public function delete(Usuario $usuario, Recurso $recurso): Response
    {
        return $this->moderaTema($usuario, $recurso->temario);
    }

    /**
     * Editar @param Recurso
     */
    public function update(Usuario $usuario, Recurso $recurso): Response
    {
        return $this->moderaTema($usuario, $recurso->temario);
    }
    
    
    private function moderaTema(Usuario $usuario, Tema $tema): Response
    {
        return $usuario->esAdmin()
            && $usuario->oposicion_moderada_id === $tema->oposicion_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
