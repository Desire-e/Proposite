<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reporte;


class ReportesController extends Controller {

    /**
     * Vista de listado de todos los reportes por oposición seleccionada
     */
    public function show(): Response {
        return Inertia::render('admin/reportes/show');
    }
    
    /**
     * Listado de todos los reportes
     */
    public function index(Request $request): Response {

    }

    /**
     * Editar reporte - marcar resuelto
     */
    public function resolve(Reporte $reporte): Response {

    }
    
}
