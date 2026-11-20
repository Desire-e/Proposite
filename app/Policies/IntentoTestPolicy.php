<?php

namespace App\Policies;

use App\Models\IntentoTest;
use App\Models\Test;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class IntentoTestPolicy
{        
    public function view(Usuario $usuario, IntentoTest $intentoTest): Response
    {
        return $this->visibleParaUsuario($usuario, $intentoTest)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function create(Usuario $usuario, Test $test): Response
    {
        return $this->visibleParaUsuario($usuario, $test)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Opositor: solo puede ver sus propios intentos, en su oposición
     * Admin: solo puede ver sus propios intentos, en la oposición que modera
     */
    private function visibleParaUsuario(Usuario $usuario, IntentoTest|Test $model): bool
    {
        $oposicionId = $usuario->comunidad()->id;
        
        if ($model instanceof Test) {
            $test = $model;
            return !$test->trashed() && 
                $oposicionId === $test->temario->oposicion_id;
        }
        else {
            $intentoTest = $model;
            return !$intentoTest->trashed()
                // solo puede ver sus propios intentos
                && $usuario->id === $intentoTest->usuario_id
                && $oposicionId === $intentoTest->test->temario->oposicion_id;
        }
    }
}
