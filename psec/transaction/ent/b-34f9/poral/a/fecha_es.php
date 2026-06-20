<?php
/**
 * Fecha larga en español (sustituto de strftime, deprecado en PHP 8.1+).
 */
function b34f9_fecha_larga(): string {
    date_default_timezone_set('America/Bogota');
    $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $t = time();
    return $dias[(int) date('w', $t)] . ', ' . (int) date('j', $t) . ' de ' . $meses[(int) date('n', $t)] . ' de ' . date('Y', $t);
}
