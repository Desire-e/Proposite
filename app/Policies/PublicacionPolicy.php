<?php

namespace App\Policies;

use App\Models\Publicacion;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Agrupación de reglas de permiso de un recurso.
 * Laravel elige la policy según el modelo que se le pasa a Gate::authorize().
 */


class PublicacionPolicy
{
    /**
     * Acciones prohibidas para admin
     * (se evalúa antes que ningún ability)
     */
    public function before(Usuario $usuario, string $ability): Response|bool|null
    {
        // Opositor, reglas normales
        if (!$usuario->esAdmin()) { return null; }

        // Admin
        return match ($ability) { 
            // Admin ve todo, incluidas las eliminadas
            'view' => Response::allow(), 
            
            // No puede
            'create', 'react' => Response::deny('Los administradores no pueden realizar esta acción.'),

            // Cualquier otra acción, reglas concretas de la ability
            default  => null,
        };
    }


    /**
     * Visualizar @param Publicacion
     */
    public function view(Usuario $usuario, Publicacion $publicacion): Response
    {
        return $this->visibleParaUsuario($usuario, $publicacion)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Crear @param Publicacion
     */
    public function create(Usuario $usuario): Response
    {
        return Response::allow();
    }

    /**
     * Reaccionar @param Publicacion
     */
    public function react(Usuario $usuario, Publicacion $publicacion): Response
    {
        return $this->visibleParaUsuario($usuario, $publicacion)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Eliminar @param Publicacion
     */
    public function delete(Usuario $usuario, Publicacion $publicacion): Response
    {
        if (!$this->visibleParaUsuario($usuario, $publicacion)) {
            return Response::denyAsNotFound();
        }

        // Publicacion eliminada no puede eliminarlo nadie
        if ($publicacion->trashed()) {
            return Response::denyAsNotFound();
        }

        // Solo admin o autor pueden eliminar
        if($usuario->esAdmin()){
            return Response::allow();
        }

        return !$this->esAutor($usuario, $publicacion)
                ? Response::deny('Solo puedes eliminar tus propias publicaciones.')
                : Response::allow();
    }



    /**
     * Visible para un opositor o admin
     */
    private function visibleParaUsuario(Usuario $usuario, Publicacion $publicacion): bool
    {
        $oposicionId = $usuario->comunidad()->id;
        
        $mismaComunidad = $publicacion->oposicion_id === $oposicionId;

        return match ($usuario->esAdmin()) {
            // Admin: ve publicaciones de misma comunidad moderada
            true => $mismaComunidad,
            
            // Opositor: ve publicaciones de misma comunidad y no eliminados
            false => $mismaComunidad && !$publicacion->trashed(),
        };
    }

    private function esAutor(Usuario $usuario, Publicacion $publicacion): bool
    {
        return $usuario->id === $publicacion->usuario_id;
    }
}
