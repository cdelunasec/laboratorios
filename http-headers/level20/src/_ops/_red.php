<?php
// Control de acceso de la consola: solo red interna 10.0.0.0/8.
// La CDN de delante manda la IP real en su propia cabecera; cualquier otra
// cabecera de IP se considera un intento de spoofing.
function ops_ip(): ?string
{
    foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_FORWARDED'] as $h) {
        if (isset($_SERVER[$h])) {
            return null;
        }
    }
    return trim($_SERVER['HTTP_TRUE_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR']);
}

function ops_exigir_red_interna(): void
{
    $ip = ops_ip();
    if ($ip === null) {
        http_response_code(403);
        lab_header('20', 'Red Caribe · Ops', 'Consola de operaciones.', 'jefe final');
        lab_alert('err', 'Cabecera de IP no autorizada: solo confiamos en la IP que manda nuestra CDN (Akamai). Intento registrado.');
        lab_footer('ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt).');
        exit;
    }
    if (!preg_match('/^10\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip)) {
        http_response_code(403);
        lab_header('20', 'Red Caribe · Ops', 'Consola de operaciones.', 'jefe final');
        lab_alert('err', "Acceso solo desde la red interna. Tu IP: $ip.");
        lab_footer('ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt).');
        exit;
    }
}
