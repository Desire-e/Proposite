<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class UsuarioPolicy
{
    public function before(Usuario $usuario, string $ability): Response|bool|null
    {
        if (!$usuario->esAdmin()) {
            return match ($ability) {
                'viewAny', 'delete', 'recover' => Response::deny('Solo los administradores pueden realizar esta operación.'),
                default => null,
            };
        }

        return match ($ability) {
            'update' => Response::deny('Los administradores no pueden realizar esta acción.'),
            default => null,
        };
    }


    public function viewAny(Usuario $usuario): Response
    {
        // Solo admin (before())
        return Response::allow();
    }


    public function view(Usuario $usuario, Usuario $perfil): Response
    {
        return ($this->visibleParaUsuario($usuario, $perfil))
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Banear = soft delete
     */
    public function delete(Usuario $usuario, Usuario $perfil): Response
    {
        // Solo admin (before())

        if (! $this->visibleParaUsuario($usuario, $perfil)){
            return Response::denyAsNotFound();
        } 

        if ($perfil->esAdmin()) {
            return Response::deny('No puedes banear a otro administrador.');
        }

        return $perfil->trashed()
            ? Response::deny('Este usuario ya está baneado.')
            : Response::allow();
    }

    public function update(Usuario $usuario, Usuario $perfil): Response {
        // Solo opositor (before()), admin no tiene perfil propio editable, solo panel

        return $perfil->id === $usuario->id 
            ? Response::allow()
            : Response::deny('No puedes modificar un perfil ajeno.');
    }

    /**
     * Suprimir baneo
     */
    public function recover(Usuario $usuario, Usuario $perfil): Response {
        // Solo admin (before())
    
        if (! $this->visibleParaUsuario($usuario, $perfil)){
            return Response::denyAsNotFound();
        }

        if ($perfil->esAdmin()) {
            return Response::deny('No puedes recuperar a otro administrador.');
        }

        return !$perfil->trashed()
            ? Response::deny('Este usuario no estaba baneado.')
            : Response::allow();
    }



    private function visibleParaUsuario(Usuario $usuario, Usuario $perfil): bool
    {
        $mismaComunidad = $perfil->oposicion_id === $usuario->comunidad()->id;
        
        return match ($usuario->esAdmin()) {
            // Admin: ve admins, ve opositores de misma comunidad moderada
            true => 
                $perfil->esAdmin() 
                || (! $perfil->esAdmin() && $mismaComunidad),
            // Opositor: ve solo opositores de misma comunidad no baneados
            false => 
                ! $perfil->esAdmin() && ! $perfil->trashed() && $mismaComunidad, 
            
            // Cualquier otro caso: no ve nada
            default => false,
        };
    }
}
