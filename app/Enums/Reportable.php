<?php

namespace App\Enums;

enum Reportable: string {
    case USUARIO = 'usuario';
    case COMENTARIO = 'comentario';
    case PUBLICACION = 'publicacion';
}
