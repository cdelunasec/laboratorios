<?php
require '/var/www/lab/lab.php';

// "Sesión" guardada entera en el navegador del cliente.
$sesion = null;
if (isset($_COOKIE['sesion'])) {
    $sesion = json_decode((string) base64_decode($_COOKIE['sesion'], true), true);
}
if (!is_array($sesion) || !isset($sesion['usuario'], $sesion['rol'])) {
    $sesion = ['usuario' => 'invitado', 'rol' => 'cliente'];
    setcookie('sesion', base64_encode(json_encode($sesion)), ['path' => '/', 'httponly' => true]);
}

lab_header('08', 'Cine Malecón', 'Cartelera y reservas.', 'la sesión vive en tu navegador');

echo '    <p>Hola, <b>' . e((string) $sesion['usuario']) . '</b> (rol: <code>' . e((string) $sesion['rol']) . "</code>).</p>\n";

if ($sesion['rol'] === 'admin') {
    lab_alert('ok', 'Panel de administración de la cartelera.');
    lab_show_flag('Código de administrador');
} else {
    echo "    <p>Cartelera de hoy: <i>Piratas del Caribe</i> (7:00 pm), <i>Sanky Panky</i> (9:30 pm).</p>\n";
    echo '    <p class="dim">La administración de la cartelera es solo para el rol admin.</p>' . "\n";
}

lab_footer('entra como administrador.');
