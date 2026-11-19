<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;


use App\Services\Admin\PanelService;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;


class PanelController extends Controller
{
    public function __construct(private PanelService $panel) {}

    
    /**
     * Panel de administración
     */
    public function index(Request $request): Response 
    {  
        $oposicionModerada = $request->user()->oposicionModerada;
        
        return Inertia::render('admin/panel', 
            $this->panel->panelData($oposicionModerada)
        );
    }
}
