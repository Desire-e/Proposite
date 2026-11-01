<?php

namespace App\Policies;

use App\Models\Comentario;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use App\Models\Publicacion;

class ComentarioPolicy
{
    public function before(Usuario $usuario, string $ability): Response|bool|null
    {
        if (!$usuario->esAdmin()) { return null; }

        return match ($ability) { 
            'view' => Response::allow(), 
            'create' => Response::deny('Los administradores no pueden realizar esta acción.'),
            default  => null,
        };
    }


    /**
     * Visualizar @param Comentario
     */
    public function view(Usuario $usuario, Comentario $comentario): Response
    {
        return $this->visibleParaOpositor($usuario, $comentario)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Publicar @param Comentario
     */
    public function create(Usuario $usuario, Publicacion $publicacion): Response
    {
        // Puede solo si puede ver la publicación
        return Gate::forUser($usuario)->inspect('view', $publicacion);
    }

    /**
     * Eliminar @param Comentario
     */
    public function delete(Usuario $usuario, Comentario $comentario): Response
    {
        // Comentario eliminado no puede eliminarlo nadie
        if ($comentario->trashed()) {
            return Response::denyAsNotFound();
        }

        // Admin puede eliminar cualquier comentario activo
        if ($usuario->esAdmin()) {
            return Response::allow();
        }

        if (!$this->visibleParaOpositor($usuario, $comentario)) {
            return Response::denyAsNotFound();
        }

        return $this->esAutor($usuario, $comentario)
            ? Response::allow()
            : Response::deny('Solo puedes eliminar tus propios comentarios.');
    }    



    /**
     * Visible para un opositor: comentario no eliminado
     * y publicación visible (no eliminada, misma oposición).
     */
    private function visibleParaOpositor(Usuario $usuario, Comentario $comentario): bool
    {
        $publicacion = $comentario->publicacion; 

        return !$comentario->trashed() 
            && $usuario->oposicion_id === $publicacion->oposicion_id;
    }

    private function esAutor(Usuario $usuario, Comentario $comentario): bool
    {
        return $usuario->id === $comentario->usuario_id;
    }

}
