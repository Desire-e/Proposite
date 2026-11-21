<?php

namespace Database\Seeders\Concerns;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Fechas aleatorias para los datos de prueba: ninguna es anterior al
 * 20 de septiembre de 2026 ni posterior al momento de ejecutar el seeder.
 */
trait FechasDemo
{
    protected function inicioDemo(): CarbonImmutable
    {
        return CarbonImmutable::create(2026, 9, 20, 9, 0, 0);
    }

    /**
     * Fecha aleatoria entre $desde y $hasta (por defecto, ahora).
     * Si $desde es posterior a $hasta, devuelve $hasta.
     */
    protected function fechaEntre(?CarbonInterface $desde = null, ?CarbonInterface $hasta = null): CarbonImmutable
    {
        $desde = CarbonImmutable::instance($desde ?? $this->inicioDemo())->max($this->inicioDemo());
        $hasta = CarbonImmutable::instance($hasta ?? now());

        if ($desde->greaterThanOrEqualTo($hasta)) {
            return $hasta;
        }

        return $desde->addSeconds(random_int(0, $hasta->getTimestamp() - $desde->getTimestamp()));
    }

    /**
     * Fecha aleatoria posterior a $desde, como mucho $maxHoras después
     * y nunca en el futuro.
     */
    protected function fechaTras(CarbonInterface $desde, int $maxHoras): CarbonImmutable
    {
        $desde = CarbonImmutable::instance($desde);

        return $this->fechaEntre($desde->addMinutes(5), $desde->addHours($maxHoras)->min(now()));
    }
}
