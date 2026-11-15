<?php
require '/var/www/lab/lab.php';

lab_header('02', 'Liceo Duarte · Boletín', 'Descarga de calificaciones.', '¿de dónde vienes?');

// "Protección": solo se descarga si el clic vino desde una página del portal.
$ref = $_SERVER['HTTP_REFERER'] ?? '';
$path = (string) parse_url($ref, PHP_URL_PATH);

if (str_starts_with($path, '/portal/')) {
    lab_alert('ok', 'Boletín generado.');
    lab_show_flag('Código del boletín');
} else {
    lab_alert('err', 'Acceso directo no permitido. Entra desde el portal estudiantil.');
}

lab_footer('descarga el boletín sin pasar por el portal (el portal pide una contraseña que no tienes).');
