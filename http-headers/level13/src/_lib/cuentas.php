<?php
// Cuentas y tokens de recuperación (JSON en /var/lib/labapp, escribible por www-data).
const DATOS = '/var/lib/labapp/datos.json';

function datos_leer(): array
{
    return json_decode((string) @file_get_contents(DATOS), true) ?: ['usuarios' => [], 'tokens' => []];
}

function datos_guardar(array $d): void
{
    file_put_contents(DATOS, json_encode($d, JSON_PRETTY_PRINT), LOCK_EX);
}
