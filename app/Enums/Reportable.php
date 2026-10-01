<?php

namespace App\Enums;

enum Reportable: string {
    case PUBLICACION = 'publicacion';        
    case COMENTARIO = 'comentario';
    case USUARIO = 'usuario';        
}

