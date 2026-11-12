<?php

namespace App\Policies;

use App\Models\IntentoTest;
use App\Models\Test;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class IntentoTestPolicy
{
    public function before(Usuario $usuario, string $ability): Response|null
    {
        // No admin - reglas normales
        if (!$usuario->esAdmin()) { return null; }

        // Admin
        return match ($ability) {
            'view' => Response::allow(),
            'create' => Response::deny('Un administrador no puede guardar intentos'),
            default  => null
        };
    }
        

    public function view(Usuario $usuario, IntentoTest $intentoTest): Response
    {
        return $this->visibleParaOpositor($usuario, $intentoTest)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function create(Usuario $usuario, Test $test): Response
    {
        return $this->visibleParaOpositor($usuario, $test)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    private function visibleParaOpositor(Usuario $usuario, IntentoTest|Test $model): bool
    {
        if ($model instanceof Test) {
            $test = $model;
            return !$test->trashed()
                && $usuario->oposicion_id === $test->temario->oposicion_id;
        }
        else {
            $intentoTest = $model;
            return !$intentoTest->trashed()
                // solo puede ver sus propios intentos
                && $usuario->id === $intentoTest->usuario_id
                && $usuario->oposicion_id === $intentoTest->test->temario->oposicion_id;
        }
    }
}
