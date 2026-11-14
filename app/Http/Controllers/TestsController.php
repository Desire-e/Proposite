<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\IntentoTest;

use Illuminate\Support\Facades\Gate;

use App\Services\TestsService;
use App\Services\IntentosTestService;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreIntentoRequest;



class TestsController extends Controller
{
    public function __construct(
        private TestsService $tests,
        private IntentosTestService $intentos,
    ) {}

    /**
     * Listado de tests
     */
    public function index(Request $request): Response 
    {
        return Inertia::render('tests/index', $this->tests->indexData($request->user()));
    }

    /**
     * Vista previa del test
     */
    public function show(Request $request, Test $test): Response 
    {
        Gate::authorize('view', $test);

        return Inertia::render('tests/show', $this->tests->showData($request->user(), $test)); 
    }


    
    /**
     * Vista realizando intento de test
     */
    public function createIntento(Request $request, Test $test): Response
    {
        Gate::authorize('view', $test);

        return Inertia::render('tests/intento', $this->intentos->createIntentoData($test));
    }

    /**
     * Generar resultado del intento del test
     * Mostrar página de resultado del intento
     */
    public function storeIntento(StoreIntentoRequest $request, Test $test): RedirectResponse
    {
        // Laravel elige la policy por el tipo del argumento, y $test es 
        // un Test, así que busca TestPolicy::intento, que no existe.
        Gate::authorize('create', [IntentoTest::class, $test]);

        $resultados = $this->intentos->storeIntentoData(
            $test,
            $request->user()->id,
            $request->validated()['respuestas']
        );

        $intento = IntentoTest::create([
            'usuario_id' => $request->user()->id,
            'test_id' => $test->id,
            'nota' => $resultados['nota']['actual']
        ]);

        // Correción mostrada temporalmente (sesión flash), ligado al intento
        session()->put("correccion_intento.{$intento->id}", $resultados);

        return to_route('tests.intento.show', $intento);
    }

    public function showIntento(IntentoTest $intentoTest): Response|RedirectResponse
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
