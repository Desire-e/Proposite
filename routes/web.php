<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Usuario;
use App\Enums\Rol;

use App\Http\Controllers\PerfilController;
use App\Http\Controllers\TemariosController;
use App\Http\Controllers\ForosController;

use App\Http\Controllers\Admin\PanelController as AdminPanelController;

// use App\Http\Controllers\PublicacionesController;
// use App\Http\Controllers\ComentariosController;
// use App\Http\Controllers\RecursosController;
// use App\Http\Controllers\TestsController;
// use App\Http\Controllers\IntentosTestController;
// use App\Http\Controllers\EstadisticasController;
// use App\Http\Controllers\ReportesController;

// use App\Http\Controllers\Admin\RecursosController as AdminRecursosController;
// use App\Http\Controllers\Admin\ReportesController as AdminReportesController;
// use App\Http\Controllers\Admin\TestsController as AdminTestsController;
// use App\Http\Controllers\Admin\UsuariosController as AdminUsuariosController;


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
    
    Route::get('/opositores/{usuario}', [PerfilController::class, 'show'])
        ->name('opositores.show');

    Route::post('/opositores/{usuario}/reportes', [PerfilController::class, 'insertReporte'])
        ->name('opositores.reportes.insert');


    // ====== Temario ======
    
    Route::get('/temario', [TemariosController::class, 'index'])
        ->name('temario.index');

    Route::patch('/temario/check', [TemariosController::class, 'toggleCheck'])
        ->name('temario.check');


    // ====== Foro ======

    Route::prefix('foro')->name('foro.')->group(function () {
        
        Route::get('/', [ForosController::class, 'index'])
            ->name('index');

        Route::prefix('publicaciones')->name('publicaciones.')->group(function () {

            Route::post('/', [ForosController::class, 'insertPublicacion'])
                ->name('insert'); // foro.publicaciones.insert

            Route::get('/{publicacion}', [ForosController::class, 'showPublicacion'])
                ->name('show'); // foro.publicaciones.show

            Route::delete('/{publicacion}', [ForosController::class, 'deletePublicacion'])
                ->name('delete'); // foro.publicaciones.delete

            Route::post('/{publicacion}/reportes', [ForosController::class, 'reportPublicacion'])
                ->name('reportes.insert'); // foro.publicaciones.reportes.insert

            Route::post('/{publicacion}/me-sirvio', [ForosController::class, 'toggleMeSirvio'])
                ->name('me-sirvio.toggle'); // foro.publicaciones.me-sirvio.toggle
            
            Route::post('/{publicacion}/comentarios', [ForosController::class, 'insertComentario'])
                ->name('comentarios.insert'); // foro.publicaciones.comentarios.insert
        });
    });


    // ====== Tests ======

    Route::prefix('tests')->name('tests.')->group(function () {
        
        // Listado de tests
        Route::get('/', [TestsController::class, 'index'])
            ->name('index'); // tests.index

        // Vista previa del test
        Route::get('/{test}/show', [TestsController::class, 'show'])
            ->name('show'); // tests.show

        // Realizando test
        Route::get('/{test}', [TestsController::class, 'createResultados'])
            ->name('resultados.create'); // tests.resultados.create

        Route::post('/{test}', [TestsController::class, 'insertResultados'])
            ->name('resultados.insert'); // tests.resultados.insert

        // Fin test
        Route::get('/{test}/resultados', [TestsController::class, 'showResultados'])
            ->name('resultados.show'); // tests.resultados.show
    });

});


// ==================
// Administrador
// ==================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/panel', [AdminPanelController::class, 'index'])
    ->name('panel');

    // otras rutas ...
});


// TODO. Revisar

Route::middleware(['auth', 'admin'])->group(function () {

    // Usuarios
    // Route::get('/usuarios', [AdminUsuariosController::class, 'index'])
    //     ->name('usuarios.index');

    // Route::patch('/usuarios/{usuario}/suspension', [AdminUsuariosController::class, 'toggleSuspendido'])
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
