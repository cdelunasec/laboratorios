<?php
require '/var/www/lab/lab.php';

lab_header('01', 'Banca Quisqueya', 'Tu banco, ahora también en el celular.', 'dime con qué navegas');

$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

// La versión web está "en mantenimiento"; el contenido completo solo se le
// entrega a la app móvil oficial, que se identifica por su User-Agent.
if (str_contains($ua, 'BanQuisApp/')) {
    lab_alert('ok', 'Bienvenido a la app móvil. Aquí tienes tu código de activación.');
    lab_show_flag('Código de activación');
} else {
    lab_alert('err', 'La banca web está en mantenimiento. Usa la app oficial BanQuisApp (versión 3 o superior) para continuar.');
    echo '    <p class="dim">Tu navegador se identificó como: <code>' . e($ua) . "</code></p>\n";
}

lab_footer('consigue el código de activación que solo ve la app móvil.');
