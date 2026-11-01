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
        // No admin - reglas normales
        if (!$usuario->esAdmin()) { return null; }

        // Admin
        return match ($ability) { 
            // Admin ve todo, incluidas las eliminadas
            'view' => Response::allow(), 
            
            // No puede
            'create' => Response::deny('Los administradores no pueden realizar esta acción.'),
            'react' => Response::deny('Los administradores no pueden realizar esta acción.'),

            // Cualquier otra acción, reglas concretas de la ability
            default  => null,
        };
    }


    /**
     * Visualizar @param Publicacion
     */
    public function view(Usuario $usuario, Publicacion $publicacion): Response
    {
        return $this->visibleParaOpositor($usuario, $publicacion)
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
        return $this->visibleParaOpositor($usuario, $publicacion)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Eliminar @param Publicacion
     */
    public function delete(Usuario $usuario, Publicacion $publicacion): Response
    {
        // Admin: puede eliminar cualquiera, salvo las ya eliminadas
        if ($usuario->esAdmin()) {
            return $publicacion->trashed()
                ? Response::deny('Esta publicación ya está eliminada.')
                : Response::allow();
        }

        if (!$this->visibleParaOpositor($usuario, $publicacion)) {
            return Response::denyAsNotFound();
        }

        return $this->esAutor($usuario, $publicacion)
            ? Response::allow()
            : Response::deny('Solo puedes eliminar tus propias publicaciones.');
    }



    /**
     * Visible para un opositor: no eliminada y de su oposición.
     */
    private function visibleParaOpositor(Usuario $usuario, Publicacion $publicacion): bool
    {
        return ! $publicacion->trashed()
            && $usuario->oposicion_id === $publicacion->oposicion_id;
    }

    private function esAutor(Usuario $usuario, Publicacion $publicacion): bool
    {
        return $usuario->id === $publicacion->usuario_id;
    }
}
