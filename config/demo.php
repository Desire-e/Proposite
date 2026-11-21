<?php

/**
 * Datos de la demostración, usados solo por los seeders.
 *
 * Las contraseñas de las cuentas de prueba no se escriben en el código
 * (el repositorio es público): se leen del .env. Se pasan por este archivo
 * de config porque, con la caché de config activa (php artisan optimize),
 * env() devuelve null fuera de los archivos de config.
 */
return [

    // Contraseña del administrador
    'admin_password' => env('ADMIN_PASSWORD'),

    // Contraseña compartida por todos los opositores de prueba
    'demo_password' => env('DEMO_PASSWORD'),

];
