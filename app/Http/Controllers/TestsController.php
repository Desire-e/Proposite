<?php

namespace App\Http\Controllers;

use App\Models\Test;

use Illuminate\Support\Facades\Gate;

use App\Services\TestsService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;


class TestsController extends Controller
{
    public function __construct( private TestsService $tests) {}

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


    

}
