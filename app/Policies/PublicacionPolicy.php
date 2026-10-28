<?php

namespace App\Policies;

use App\Models\Publicacion;
use App\Models\Usuario;
use App\Enums\Rol;
use Illuminate\Auth\Access\Response;

/**
 * Agrupación de reglas de permiso de un recurso.
 * Laravel elige la policy según el modelo que se 
 * le pasa a Gate::authorize().
 */

class PublicacionPolicy
{

    /**
     * Primer método ejecutado en la policy.
     * 
     * Recibe @param usuario y @param ability (nombre de la acción 
     * que se está comprobando).
     * 
     * @return true Si es admin y la acción es view o delete, Laravel 
     * autoriza sin llamar al método correspondiente.
     */
    public function before(Usuario $usuario, string $ability): Response|bool|null
    {
        // Si no es admin, reglas normales
        if ($usuario->rol !== Rol::ADMIN) {
            return null;
        }

        // Admin: puede ver y eliminar cualquier publicación
        if ($ability === 'view' || $ability === 'delete') {
            return true;
        }

        // Admin: no puede comentar ni reportar
        if ($ability === 'comment' || $ability === 'report') {
            return Response::deny('Los administradores no pueden realizar esta acción.');
        }

        // Cualquier otra acción: regla normal
        return null;
    }

    /**
     * No puede ver/interactuar con publicaciones de distinta oposición
     */
    public function view(Usuario $usuario, Publicacion $publicacion): Response
    {
        return $usuario->oposicion_id === $publicacion->oposicion_id
            ? Response::allow() // continúa
            : Response::denyAsNotFound(); // 404
    }

    public function comment(Usuario $usuario, Publicacion $publicacion): Response 
    { 
        return $this->view($usuario, $publicacion); 
    }
    
    /**
     * No puede reportar publicaciones propias, 
     * publicaciones de otra oposición, 
     * ni dos veces a la misma
     */
    public function report(Usuario $usuario, Publicacion $publicacion): Response
    {
        $acceso = $this->view($usuario, $publicacion);
        if ($acceso->denied()) return $acceso;

        if($publicacion->usuario_id === $usuario->id){
            return Response::deny('Solo puedes reportar publicaciones que no sean las tuyas.'); // 403
        } 

        $yaReportada = $publicacion->reportesRecibidos()
            ->where('usuario_reporta_id', $usuario->id)
            ->exists();

        return $yaReportada
            ? Response::deny('Ya habías reportado esta publicación.')
            : Response::allow();
    }

    /**
     * No puede eliminar publicaciones de otro usuario
     */
    public function delete(Usuario $usuario, Publicacion $publicacion): Response
    {
        return $usuario->id === $publicacion->usuario_id
            ? Response::allow()
            : Response::deny('Solo puedes eliminar tus propias publicaciones.');
    }
}
