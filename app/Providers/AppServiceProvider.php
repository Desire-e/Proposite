<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Publicacion;
use App\Models\Comentario;
use App\Models\Usuario;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {

        /**
         * Custom Polymorphic Types
         * https://laravel.com/framework/docs/12.x/eloquent-relationships#custom-polymorphic-types
         * 
         * Por defecto Laravel usa el nombre de clase completo para almacenar el "tipo" del modelo relacionado.
         * 
         */

        Relation::enforceMorphMap([
            'publicacion' => Publicacion::class,
            'comentario' => Comentario::class,
            'usuario' => Usuario::class,
        ]);
    }
}
