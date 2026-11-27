<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Usuario;
use App\Enums\Rol;

use App\Http\Controllers\TemariosController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\OpositoresController;
use App\Http\Controllers\ForosController;
use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\IntentosTestController;

use App\Http\Controllers\Admin\PanelController as AdminPanelController;
use App\Http\Controllers\Admin\ComunidadController as AdminComunidadController;
use App\Http\Controllers\Admin\RecursosController as AdminRecursosController;
use App\Http\Controllers\Admin\ReportesController as AdminReportesController;
use App\Http\Controllers\Admin\OpositoresController as AdminUsuariosController;
use App\Http\Controllers\Admin\TestsController as AdminTestsController;


/*****************************
 * RUTAS PRINCIPALES 
 *****************************/

// ==================
// Inicio
// ==================

Route::get('/', function () {

    // Inicio en perfil (autenticado)
    if (auth()->check()) { return redirect()->route('dashboard'); }
    
    // Inicio en landing (no autenticado)
    return Inertia::render('welcome', [
        'usersNumber' => Usuario::count(),
    ]);

})->name('welcome'); // nombre interno para hacer route('welcome')


// Router por rol: solo redirige
Route::middleware('auth')->get('/dashboard', function () {
    
    /**
     * Comprobaciones de rol
     * 
     * Alias de los middlewares: mira en bootstrap/app.php cómo llamaste a los alias de 
     * rol (admin, opositor u otros). Usa exactamente esos nombres.
     * 
     * Si rol está casteado a enum en el modelo, === 'administrador' será siempre falso. 
     * En ese caso compara con el caso del enum, por ejemplo Rol::Administrado
     */
    return auth()->user()->rol === Rol::ADMIN
        ? redirect()->route('admin.panel')
        // : redirect()->route('opositor.perfil');
        : redirect()->route('perfil.show');
    
})->name('dashboard');

// ==================
// Generales autenticado
// ==================

Route::middleware(['auth', 'oposicion.moderada'])->group(function () {

    // ====== Foro ======

    Route::prefix('foro')->name('foro.')->group(function () {
        
        Route::get('/', [ForosController::class, 'index'])
            ->name('index');

        // Acciones para publicaciones y sus comentarios

        Route::prefix('publicaciones')->name('publicaciones.')->group(function () {
            
            Route::get('/{publicacion}', [PublicacionesController::class, 'show'])
                ->name('show'); // foro.publicaciones.show
            
            Route::post('/', [PublicacionesController::class, 'store'])
                ->name('store'); // foro.publicaciones.store

            Route::post('/{publicacion}/me-sirvio', [PublicacionesController::class, 'toggleMeSirvio'])
                ->name('me-sirvio.toggle'); // foro.publicaciones.me-sirvio.toggle

            Route::delete('/{publicacion}', [PublicacionesController::class, 'delete'])
                ->name('delete'); // foro.publicaciones.delete

            Route::post('/{publicacion}/reportes', [ReportesController::class, 'storePublicacion'])
                ->name('reportes.store'); // foro.publicaciones.reportes.store

            Route::post('/{publicacion}/comentarios', [ComentariosController::class, 'store'])
                ->name('comentarios.store'); // foro.publicaciones.comentarios.store
        });

        // Acciones concretas para comentarios

        Route::prefix('comentarios')->name('comentarios.')->group(function () {
            Route::delete('/{comentario}', [ComentariosController::class, 'delete'])
                ->name('delete'); // foro.comentarios.delete

            Route::post('/{comentario}/reportes', [ReportesController::class, 'storeComentario'])
                ->name('reportes.store'); // foro.comentarios.reportes.store
        });
    });
    
    // ====== Perfil ajeno ======

    Route::get('/opositores/{usuario}', [OpositoresController::class, 'show'])
        ->name('opositores.show');

    // ====== Temario ======

    Route::get('/temario', [TemariosController::class, 'index'])
        ->name('temario.index');


    // TODO. PONER PARA ADMIN PERO SIN REGISTRAR RESULTADOS
    
    // ====== Tests ======

    Route::prefix('tests')->name('tests.')->group(function () {
        
        // Listado de tests
        Route::get('/', [TestsController::class, 'index'])
            ->name('index'); // tests.index

        // Vista previa del test
        Route::get('/{test}/show', [TestsController::class, 'show'])
            ->name('show'); // tests.show

        // Realizando intento de test
        Route::get('/{test}/intento', [IntentosTestController::class, 'create'])
            ->name('intento.create'); // tests.intento.create

        // Insertar intento de test
        Route::post('/{test}/intento', [IntentosTestController::class, 'store'])
            ->name('intento.store'); // tests.intento.store

        // Ver resultado de un intento concreto
        Route::get('intento/{intentoTest}', [IntentosTestController::class, 'show'])
            ->name('intento.show'); // tests.intento.show
    });

});


// ==================
// Opositor
// ==================


Route::middleware(['auth', 'opositor'])->group(function () {

    // ====== Perfil propio ======

    Route::get('/perfil', [PerfilController::class, 'show'])
        ->name('perfil.show');
    
    // Editar perfil
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])
        ->name('perfil.edit');

    Route::patch('/perfil', [PerfilController::class, 'update'])
        ->name('perfil.update');

    // ====== Perfil ajeno ======
    
    Route::post('/opositores/{usuario}/reportes', [ReportesController::class, 'storeUsuario'])
        ->name('opositores.reportes.store');

    // ====== Temario ======

    Route::patch('/temario/check', [TemariosController::class, 'toggleCheck'])
        ->name('temario.check');
});


// ==================
// Administrador
// ==================

Route::middleware(['auth', 'admin', 'oposicion.moderada'])->prefix('admin')->name('admin.')->group(function () {

    // ====== Panel de administración en la oposición ======

    Route::get('/panel', [AdminPanelController::class, 'index'])
        ->name('panel');

    // ====== Gestionar temario ======

    Route::prefix('temario')->name('temario.')->group(function () {

        // Añadir, editar, eliminar recursos de un tema
        Route::post('/{tema}/recursos', [AdminRecursosController::class, 'store'])
            ->name('recursos.store'); // admin.temario.recursos.store

        Route::delete('/recursos/{recurso}', [AdminRecursosController::class, 'delete'])
            ->name('recursos.delete'); // admin.temario.recursos.delete
        
        Route::patch('/recursos/{recurso}', [AdminRecursosController::class, 'update'])
            ->name('recursos.update'); // admin.temario.recursos.update
    });

    // ====== Gestionar reportes ======

    Route::prefix('reportes')->name('reportes.')->group(function () {

        // Omitir reporte
        Route::post('/{reporte}', [AdminReportesController::class, 'omit'])
            ->name('omit'); // admin.reportes.omit

        // Eliminar comentario / publicación reportado
        Route::delete('/{reporte}/contenido', [AdminReportesController::class, 'deleteContent'])
            ->name('delete'); // admin.reportes.delete
        
        // Banear usuario reportado
        Route::delete('/{reporte}/usuario', [AdminReportesController::class, 'banUsuario'])
            ->name('ban'); // admin.reportes.ban
    });


    // ====== Listado y gestionar usuarios ======

    Route::prefix('usuarios')->name('usuarios.')->group(function () {

        Route::get('/', [AdminUsuariosController::class, 'index'])
            ->name('index'); // admin.usuarios.index

        Route::patch('/{usuario}', [AdminUsuariosController::class, 'recover'])
            ->withTrashed() // La ruta necesita withTrashed(). Sin él, {usuario} no encuentra a un usuario baneado y devuelve 404
            ->name('recover'); // admin.usuarios.recover

        // Banear usuario NO reportado
        Route::delete('/{usuario}', [AdminUsuariosController::class, 'ban'])
            ->name('ban'); // admin.usuarios.ban
    });

    // ====== Gestionar tests ======

    Route::prefix('tests')->name('tests.')->group(function () {

        // Añadir, editar, eliminar test
        Route::get('/create', [AdminTestsController::class, 'create'])
            ->name('create'); // admin.tests.create
        Route::post('/', [AdminTestsController::class, 'store'])
            ->name('store'); // admin.tests.store

        Route::get('/{test}/edit', [AdminTestsController::class, 'edit'])
            ->name('edit'); // admin.tests.edit
        Route::patch('/{test}', [AdminTestsController::class, 'update'])
            ->name('update'); // admin.tests.update

        Route::delete('/{test}', [AdminTestsController::class, 'delete'])
            ->name('delete'); // admin.tests.delete        
    });
});


// Tras inicio de sesión administrador - seleccionar oposición a moderar
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('welcome', [AdminComunidadController::class, 'index'])
        ->name('welcome'); // admin.welcome

    // seleccionar / cambiar oposición a moderar
    Route::post('select', [AdminComunidadController::class, 'select'])
        ->name('select'); // admin.select
});




// TODO.

Route::middleware(['auth', 'admin'])->group(function () {

    // Usuarios
    // Route::get('/usuarios', [AdminUsuariosController::class, 'index'])
    //     ->name('usuarios.index');

    // Route::patch('/usuarios/{usuario}/suspension', [AdminUsuariosController::class, 'toggleBaneado'])
    //     ->name('usuarios.suspension');


    // Tests
    // Route::get('/tests/create', [AdminTestsController::class, 'create'])
    //     ->name('tests.create');

    // Route::post('/tests', [AdminTestsController::class, 'store'])
    //     ->name('tests.store');

    // Route::get('/tests/{test}/edit', [AdminTestsController::class, 'edit'])
    //     ->name('tests.edit');

    // Route::put('/tests/{test}', [AdminTestsController::class, 'update'])
    //     ->name('tests.update');

    // Route::delete('/tests/{test}', [AdminTestsController::class, 'destroy'])
        // ->name('tests.destroy');


    // Recursos
    // Route::post('/recursos', [AdminRecursosController::class, 'store'])
    //     ->name('recursos.store');

    // Route::delete('/recursos/{recurso}', [AdminRecursosController::class, 'destroy'])
    //     ->name('recursos.destroy');


    // Reportes
    // Route::get('/reportes', [AdminReportesController::class, 'index'])
    //     ->name('reportes.index');

    // Route::patch('/reportes/{reporte}/resolver', [AdminReportesController::class, 'resolve'])
    //     ->name('reportes.resolve');
});


//////////////////////////////////////////
// Nota: Ejemplo de ruta con middleware 
//////////////////////////////////////////
/*

// 'auth' - middleware predeterminado authentication

Route::middleware(['auth' , 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Solo administradores

Route::middleware(['auth', 'admin'])->group(function () {
    // Rutas de administración
});

// Solo opositores

Route::middleware(['auth', 'opositor'])->group(function () {
    // Rutas de opositores
});

// Cualquier usuario autenticado

Route::middleware('auth')->group(function () {
    // Rutas comunes para usuarios registrados
});

*/


// Esto importa las rutas que están definidas en esos archivos.
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
