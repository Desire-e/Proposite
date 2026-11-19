<?php

namespace App\Services;
use Illuminate\Support\Collection;

use App\Models\Oposicion;
use App\Models\Usuario;
use App\Models\Tema;

class TemarioService
{
    /**
     *  Temario, con recursos y estados marcados de cada uno 
     */
    public function temarioData(Oposicion $oposicion, Usuario $usuario): Collection
    {
        // Temas marcados como estudiados:
        // guarda el estado de cada tema en una colección indexada 
        // por el ID del tema. [ '1'=>true, '1.1.'=> false, ... ]
        $checked = $usuario->checkedTemario() 
            ->pluck('checks_temario.checked', 'temarios.id');


        return $oposicion->temario()
            ->with('recursos')->get() // carga también recursos de cada tema
            ->sortBy('numero_tema', SORT_NATURAL)
            ->values()  // se reinician los índices del array para dejarlo ordenado
            ->map(fn (Tema $tema) => [
                'id' => $tema->id,
                'bloque' => $tema->bloque,
                'numero_tema' => $tema->numero_tema,
                'titulo' => $tema->titulo,
                'recursos' => $tema->recursos,
                'checked' => 
                    (bool) ($checked[$tema->id] ?? false), // devuelve el estado o false si no existe
        ]);
    }
}