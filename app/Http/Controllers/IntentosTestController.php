<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\IntentoTest;

use Illuminate\Support\Facades\Gate;

use App\Services\IntentosTestService;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreIntentoRequest;

class IntentosTestController extends Controller
{

    public function __construct( private IntentosTestService $intentos ) {}

    /**
     * Vista realizando intento de test
     */
    public function create(Request $request, Test $test): Response
    {
        Gate::authorize('view', $test);

        return Inertia::render('tests/intento', $this->intentos->createIntentoData($test));
    }

    /**
     * Generar resultado del intento del test
     * Mostrar página de resultado del intento
     * 
     * Opositor: corrige, guarda el intento y redirige al resultado.
     * Administrador: modo prueba, corrige y muestra el resultado sin guardar nada.
     */
    public function store(StoreIntentoRequest $request, Test $test): Response|RedirectResponse
    {

        $respuestas = $request->validated()['respuestas'];

        if ($request->user()->esAdmin()) {
            // No crea intento: solo necesita poder ver el test 
            // (modo de prueba)
            Gate::authorize('view', $test);

            // Va directo a resultado, sin generar test
            return Inertia::render('tests/resultado', [
                ...$this->intentos->corregir($test, null, $respuestas),
                'prueba' => true,
            ]);
        }
        // Laravel elige la policy por el tipo del argumento, y $test es 
        // un Test, así que busca TestPolicy::intento, que no existe.
        Gate::authorize('create', [IntentoTest::class, $test]);

        $resultados = $this->intentos->registrar(
            $test,
            $request->user(),
            $respuestas
        );

        return to_route('tests.intento.show', $resultados);
    }

    public function show(IntentoTest $intentoTest): Response|RedirectResponse
    {
        Gate::authorize('view', $intentoTest);

        // get() lee y persiste hasta que la sesión caduca (por defecto
        // 120 minutos, SESSION_LIFETIME en .env), o cuando se cierra sesión
        // Así el usuario puede recargar.
        $resultados = session()->get("correccion_intento.{$intentoTest->id}");

        // Cuando caduca sesión, se redirige a la vista previa del test
        // (devuelve RedirectResponse, por eso el tipo de retorno admite ambos)
        if (!$resultados) { return to_route('tests.show', $intentoTest->test_id); }

        return Inertia::render('tests/resultado', $resultados);
    }
}
