<?php

namespace App\Policies;

use App\Models\Comentario;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use App\Models\Publicacion;

class ComentarioPolicy
{
    public function before(Usuario $usuario, string $ability): Response|null
    {
        // Opositor
        if (!$usuario->esAdmin())  return null; 

        // Admin
        if ($ability === 'create') {
            return Response::deny('Los administradores no pueden realizar esta acción.');
        }
        else return null;
    }


    /**
     * Visualizar @param Comentario
     */
    public function view(Usuario $usuario, Comentario $comentario): Response
    {
        return $this->visibleParaUsuario($usuario, $comentario)
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
        if (!$this->visibleParaUsuario($usuario, $comentario)) {
            return Response::denyAsNotFound();
        }

        // Comentario eliminado no puede eliminarlo nadie
        if ($comentario->trashed()) {
            return Response::denyAsNotFound();
        }

        // Solo admin o autor pueden eliminar
        if($usuario->esAdmin()){
            return Response::allow();
        }

        return !$this->esAutor($usuario, $comentario)
            ? Response::deny('Solo puedes eliminar tus propios comentarios.')
            : Response::allow();
    }    

    

    /**
     * Visible para un opositor o admin
     */
    private function visibleParaUsuario(Usuario $usuario, Comentario $comentario): bool
    {
        $oposicionId = $usuario->comunidad()->id;
        
        $mismaComunidad = $comentario->publicacion()->withTrashed()->value('oposicion_id') === $oposicionId;

        return match ($usuario->esAdmin()) {
            // Admin: ve comentarios de misma comunidad moderada
            true => $mismaComunidad,
            
            // Opositor: ve comentarios de misma comunidad y no eliminados
            false => $mismaComunidad && !$comentario->trashed(),
        };
    }

    private function esAutor(Usuario $usuario, Comentario $comentario): bool
    {
        return $usuario->id === $comentario->usuario_id;
    }
}