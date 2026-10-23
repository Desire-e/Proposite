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



// ==================
// Router por rol: solo redirige
// ==================

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
        : redirect()->route('opositor.perfil');
    
})->name('dashboard');



// ==================
// Opositor
// ==================

Route::middleware(['auth', 'opositor'])->name('opositor.')->group(function () {

    // --- Perfil propio
    Route::get('/perfil', [PerfilController::class, 'index'])
        ->name('perfil'); // opositor.perfil

    // Editar perfil
    Route::get('/perfil/edit', [PerfilController::class, 'edit'])
        ->name('perfil.edit');

    Route::post('/perfil', [PerfilController::class, 'update'])
        ->name('perfil.update');


    // --- Perfil ajeno
    Route::get('/opositores/{usuario}', [PerfilController::class, 'show'])
        ->name('perfil.show');

    Route::post('/opositores/{usuario}/reportar', [PerfilController::class, 'report'])
        ->name('perfil.reportar');
        
});

Route::middleware(['auth', 'opositor'])->group(function () {
    
    // --- Temario
    
    Route::get('/temario', [TemariosController::class, 'index'])
        ->name('temario.index');

    Route::patch('/temario/check', [TemariosController::class, 'toggleCheck'])
        ->name('temario.check');

    // --- Foro
    
    Route::get('/foro', [ForosController::class, 'index'])
        ->name('foro.index');

    Route::post('/foro', [ForosController::class, 'insertPublicacion'])
        ->name('foro.publicacion.insertPublicacion');

    Route::delete('/foro/publicaciones/{publicacion}/eliminar', [ForosController::class, 'deletePublicacion'])
        ->name('foro.publicacion.eliminar');

    Route::post('/foro/publicaciones/{publicacion}/reportar', [ForosController::class, 'reportPublicacion'])
        ->name('foro.publicacion.reportar');

    Route::post('/foro/publicaciones/{publicacion}/me-sirvio', [ForosController::class, 'toggleMeSirvio'])
    ->name('foro.publicacion.meSirvio');

    // --- Tests


});


// ==================
// Administrador
// ==================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/panel', [AdminPanelController::class, 'index'])
    ->name('panel');

    // otras rutas ...
});



///////////////////////////////////////////////////////////////////////////////

// ==================
// Opositor
// ==================
// TODO. Revisar
Route::middleware(['auth', 'opositor'])->group(function () {
    // Foro / publicaciones


    // Route::get('/foro/create', [ForosController::class, 'create'])
    //     ->name('foro.create');

    // Route::post('/foro', [ForosController::class, 'store'])
    //     ->name('foro.store');

    // Route::get('/foro/{publicacion}', [ForosController::class, 'show'])
    //     ->name('foro.show');

    // Route::delete('/foro/{publicacion}', [ForosController::class, 'destroy'])
    //     ->name('foro.destroy');

    // Route::post('/foro/{publicacion}/reaccion', [ForosController::class, 'toggleReaccion'])
    //     ->name('foro.reaccion');


    // Comentarios
    // Route::post('/foro/{publicacion}/comentarios', [ComentariosController::class, 'store'])
    //     ->name('comentarios.store');

    // Route::delete('/comentarios/{comentario}', [ComentariosController::class, 'destroy'])
    //     ->name('comentarios.destroy');





    // Recursos
    // Route::get('/recursos', [RecursosController::class, 'index'])
    //     ->name('recursos.index');


    // Tests
    // Route::get('/tests', [TestsController::class, 'index'])
    //     ->name('tests.index');

    // Route::get('/tests/{test}', [TestsController::class, 'show'])
    //     ->name('tests.show');


    // Intentos de test
    // Route::post('/tests/{test}/intentos', [IntentosTestController::class, 'store'])
    //     ->name('intentos.store');

    // Route::get('/intentos/{intento}', [IntentosTestController::class, 'show'])
    //     ->name('intentos.show');


    // Estadísticas
    // Route::get('/estadisticas', [EstadisticasController::class, 'show'])
    //     ->name('estadisticas.show');


    // Reportes
    // Route::post('/reportes', [ReportesController::class, 'store'])
    //     ->name('reportes.store');
});



// ==================
// Administrador
// ==================
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
