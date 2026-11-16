<?php
// SoporteYa: mesa de ayuda (nivel 2 del lab de webs completas).
require __DIR__ . '/lab.php';

const LAB_APP = 'SoporteYa';
const LAB_HINT = 'la oficina confía en lo que le dices';
const LAB_EXTRA = '';

const SOPORTE = '/var/www/soporte';
const BITACORA = SOPORTE . '/logs/acceso.log';

// Bitácora de accesos propia de la app (Apache manda la suya a la consola).
function registrar_acceso(): void
{
    $linea = sprintf(
        "[%s] %s \"%s %s\" \"%s\"\n",
        date('d/M/Y:H:i:s O'),
        $_SERVER['REMOTE_ADDR'] ?? '-',
        $_SERVER['REQUEST_METHOD'] ?? '-',
        $_SERVER['REQUEST_URI'] ?? '-',
        $_SERVER['HTTP_USER_AGENT'] ?? '-'
    );
    @file_put_contents(BITACORA, $linea, FILE_APPEND | LOCK_EX);
}

// IP del cliente. Detrás del balanceador la real viene en X-Forwarded-For.
function ip_cliente(): string
{
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($xff !== '') {
        return trim(explode(',', $xff)[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

function desde_oficina(): bool
{
    $ip = ip_cliente();
    return $ip === '127.0.0.1' || str_starts_with($ip, '10.');
}

function tickets(): array
{
    $t = glob(SOPORTE . '/tickets/*.txt') ?: [];
    sort($t);
    return array_map('basename', $t);
}

function cabecera(string $tagline): void
{
    registrar_acceso();
    lab_header($tagline, ['/' => 'Inicio', '/nuevo.php' => 'Abrir ticket', '/ticket.php' => 'Consultar ticket']);
}

function exigir_oficina(): void
{
    if (desde_oficina()) {
        return;
    }
    http_response_code(403);
    cabecera('Herramientas internas del equipo de soporte.');
    lab_alert('err', 'Acceso restringido a la red de la oficina (10.0.0.0/8). Tu IP: ' . ip_cliente());
    lab_footer();
    exit;
}
