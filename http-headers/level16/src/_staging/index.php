<?php
require '/var/www/lab/lab.php';

header('Vary: Accept, Forwarded');

// Paso 1: solo red interna. Tras la migración del balanceador, la IP del cliente
// llega únicamente en la cabecera estándar; X-Forwarded-For ya no se mira.
$ip = $_SERVER['REMOTE_ADDR'];
if (preg_match('/(?:^|[;,\s])for="?\[?([0-9a-fA-F:.]+)/i', $_SERVER['HTTP_FORWARDED'] ?? '', $m)) {
    $ip = $m[1];
}
$interna = (bool) preg_match('/^10\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip);

$quiereJson = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

if (!$interna) {
    $msg = "Acceso solo desde la red interna (10.0.0.0/8). Tu IP: $ip.";
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $msg .= ' X-Forwarded-For es una cabecera obsoleta en este balanceador; usamos la estándar.';
    }
    if ($quiereJson) {
        lab_json(['error' => $msg], 403);
    }
    http_response_code(403);
    lab_header('16', 'Staging API', 'Entorno de pruebas.', 'todo lo anterior');
    lab_alert('err', $msg);
    lab_footer('consigue el inventario de secretos de la tienda.');
    exit;
}

// Paso 2: el inventario de secretos solo se entrega a clientes de la API.
if ($quiereJson) {
    lab_json(['entorno' => 'staging', 'secretos' => ['pasarela_pagos' => lab_flag()]]);
}

lab_header('16', 'Staging API', 'Entorno de pruebas.', 'todo lo anterior');
lab_alert('ok', "Red interna ($ip).");
echo "    <p>El inventario de secretos solo se entrega a clientes de la API (JSON). Los navegadores ven esta página.</p>\n";
lab_footer('consigue el inventario de secretos de la tienda.');
