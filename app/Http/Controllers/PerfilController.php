<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Usuario;
use App\Models\Avatar;
use App\Models\IntentoTest;
use App\Models\Publicacion;
use App\Models\Comentario;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class PerfilController extends Controller
{
    /**
     * Carga los datos del usuario y renderiza perfil
     */
    public function index(): Response 
    {
        $user = Auth::user();

        $avatarId = $user->avatar_id;
        $avatar = Avatar::find($avatarId);

        $testsRealizados = IntentoTest::where('usuario_id', $user->id)->distinct('test_id')->count('test_id');            
        
        $publicaciones = Publicacion::where('usuario_id', $user->id)->count();
        
        $comentarios = Comentario::where('usuario_id', $user->id)->count();

        $userPublicaciones = Publicacion::where('usuario_id', $user->id)
            ->pluck('id'); // valores de una columna concreta
        $respuestas = Comentario::whereIn('publicacion_id', $userPublicaciones) // busca  asociados a los publicacion_id
            ->count();

        $avatares = Avatar::all();
        
        return Inertia::render('opositor/perfil', [
            'user' => [
                'name' => $user->name ?? null,
                'email'=> $user->email ?? null,
                'avatar'=> $avatar ?? null,
                'biografia'=> $user->biografia ?? null,
                'fecha_convocatoria'=> $user->fecha_convocatoria ?? null,
                'tests' => $testsRealizados,
                'publicaciones' => $publicaciones,
                'respuestas' => $respuestas,
            ],
            'avatares' => $avatares,
        ]);
    }


    /**
     * Actualiza los datos del usuario
     */

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:150', 
                Rule::unique('usuarios', 'name') // nombre único
                    ->ignore($request->user()->id), // excluye al usuario autenticado
            ],
            'avatar_id' => ['nullable', 'uuid', 'exists:avatares,id'],
            'biografia' => ['nullable', 'string'],
            'fecha_convocatoria' => ['nullable', 'date', 'after:today'], 
        ], [
            'fecha_convocatoria.after' => 'La fecha de convocatoria debe ser posterior a hoy.',
        ]);

        $user = $request->user(); // usuario autenticado que hizo la request

        $user->name = $validated['name'];
        $user->avatar_id = $validated['avatar_id'];
        $user->biografia = $validated['biografia'];
        $user->fecha_convocatoria = $validated['fecha_convocatoria'];
        
        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }


}
