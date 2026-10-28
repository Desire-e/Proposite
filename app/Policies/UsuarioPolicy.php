<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\Response;
use App\Enums\Rol;

class UsuarioPolicy
{
    public function before(Usuario $usuario, string $ability): Response|bool|null
    {
        // Si no es admin, reglas normales
        if ($usuario->rol !== Rol::ADMIN){
            return null;
        } 

        // Admin: no puede reportar
        if ($ability === 'report') {
            return Response::deny('Los administradores no pueden realizar esta acción.');
        }

        return null;
    }

    public function view(Usuario $usuario, Usuario $perfil): Response
    {
        // Los administradores no tienen perfil público
        if ($perfil->rol === Rol::ADMIN) {
            return Response::denyAsNotFound(); // responde 404 not found
        }
        
        // El admin ve cualquier opositor
        if ($usuario->rol === Rol::ADMIN) {
            return Response::allow();
        }

        // Un opositor solo ve perfiles activos de su misma oposición
        return  (!$perfil->suspendido) && ($usuario->oposicion_id === $perfil->oposicion_id)
            ? Response::allow()
            : Response::denyAsNotFound(); // 404
    }

    public function report(Usuario $usuario, Usuario $reportado): Response
    {
        $acceso = $this->view($usuario, $reportado);

        if ($acceso->denied()) return $acceso;

        if ($usuario->id === $reportado->id) {
            return Response::deny('No puedes reportarte a ti mismo.');
        }

        $yaReportado = $reportado->reportesRecibidos()
            ->where('usuario_reporta_id', $usuario->id)
            ->exists();

        return $yaReportado
            ? Response::deny('Ya habías reportado a este usuario.')
            : Response::allow();
    }
}
