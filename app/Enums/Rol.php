<?php

namespace App\Enums;

/**
 * Backed Enums: Enumerations with associated string or integer values.
 * 
 * Documentación: 
 * https://medium.com/@zulfikarditya/using-php-enums-in-laravel-12-a-comprehensive-guide-af75689f88e8
 */
enum Rol: string {
    case ADMIN = 'administrador';
    case OPOSITOR = 'opositor';    
}



