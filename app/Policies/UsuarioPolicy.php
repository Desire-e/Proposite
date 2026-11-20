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
                'view' => null, 
                'delete' => Response::deny('Solo los administradores pueden realizar esta operación.'), 
                'recover' => Response::deny('Solo los administradores pueden realizar esta operación.'), 
                default  => null,
            };            
        }

        return match ($ability) { 
            'view' => Response::allow(), 
            default  => null,
        };
    }

    public function view(Usuario $usuario, Usuario $perfil): Response
    {
        // Los administradores no tienen perfil público
        if ($perfil->esAdmin()) {
            return $usuario->esAdmin()
                ? Response::allow()
                : Response::denyAsNotFound(); // responde 404 not found
        }

        // Un opositor solo ve perfiles activos de su misma oposición
        return ($this->visibleParaOpositor($usuario, $perfil))
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Banear = soft delete
     */
    public function delete(Usuario $usuario, Usuario $perfil): Response
    {
        if ($perfil->esAdmin()) {
            return Response::deny('No puedes banear a otro administrador.');
        }

        return $perfil->trashed()
            ? Response::deny('Este usuario ya está baneado.')
            : Response::allow();
    }

    public function update(Usuario $usuario, Usuario $perfil): Response {
        return $perfil->id === $usuario->id 
            ? Response::allow()
            : Response::deny('No puedes modificar un perfil ajeno.');
    }

    /**
     * Suprimir baneo
     */
    public function recover(Usuario $usuario, Usuario $perfil): Response {
        return !$perfil->trashed()
            ? Response::deny('Este usuario no estaba baneado.')
            : Response::allow();
    }



    private function visibleParaOpositor(Usuario $usuario, Usuario $perfil): bool
    {
        return !$perfil->trashed() 
            && $usuario->oposicion_id === $perfil->oposicion_id;
    }
}
