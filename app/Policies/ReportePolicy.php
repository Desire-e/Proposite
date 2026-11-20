<?php

namespace App\Policies;

use App\Models\Comentario;
use App\Models\Publicacion;
use App\Models\Reporte;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class ReportePolicy
{

    public function before(Usuario $usuario, string $ability): Response|bool|null
    {
        if (!$usuario->esAdmin()) {
            return match ($ability) { 
                'view' => Response::deny('Solo los administradores pueden realizar esta acción.'),
                'solve' => Response::deny('Solo los administradores pueden realizar esta acción.'),
                default  => null,
            };            
        }

        return match ($ability) { 
            'report' => Response::deny('Los administradores no pueden realizar esta acción.'), 
            default  => null,
        };
    }

    public function view(Usuario $usuario, Reporte $reporte): Response
    {
        return Response::allow();
    }

    public function report(Usuario $usuario, Publicacion|Comentario|Usuario $reportable): Response
    {
        // Deniega: otra oposición (404), eliminado (404), autor baneado (404)
        $acceso = Gate::forUser($usuario)->inspect('view', $reportable);
        if ($acceso->denied()) {
            return $acceso;
        }

        // Deniega si es a si mismo
        $messageEsPropio = '';

        if($this->esPropio($usuario, $reportable)) {
            if ($reportable instanceof Usuario) {
                $messageEsPropio = 'No puedes reportarte a ti mismo.';
            } elseif ($reportable instanceof Publicacion) {
                $messageEsPropio = 'Solo puedes reportar publicaciones que no sean las tuyas.';
            } elseif ($reportable instanceof Comentario) {
                $messageEsPropio = 'Solo puedes reportar comentarios que no sean los tuyos.';
            } else {
                $messageEsPropio = 'No puedes reportar este recurso.';
            }

            return Response::deny($messageEsPropio);
        }

        return Response::allow();
    }

    public function solve(Usuario $usuario, Reporte $reporte): Response
    {
        return $reporte->resuelto()
            ? Response::deny('Este reporte ya fue resuelto.')
            : Response::allow();
    }



    private function esPropio(Usuario $usuario, Publicacion|Comentario|Usuario $reportable): bool
    {
        return $reportable instanceof Usuario
            ? $reportable->is($usuario)
            : $reportable->usuario_id === $usuario->id;
    }

}