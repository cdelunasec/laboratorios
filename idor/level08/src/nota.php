<?php
require '/var/www/lab/lab.php';

$me = require_login();

// Módulo de permisos "reutilizado" de otro proyecto: lee la query string cruda
// y se queda con la PRIMERA aparición del parámetro.
function primer_parametro(string $nombre): ?string
{
    foreach (explode('&', $_SERVER['QUERY_STRING'] ?? '') as $par) {
        [$k, $v] = array_pad(explode('=', $par, 2), 2, '');
        if (urldecode($k) === $nombre) {
            return urldecode($v);
        }
    }
    return null;
}

lab_header('Nota');

$check = query('SELECT user_id FROM notas WHERE id = ?', [primer_parametro('id') ?? 0])->fetch();
if (!$check || (int) $check['user_id'] !== (int) $me['id']) {
    lab_alert('err', 'Esa nota no es tuya.');
    lab_footer();
    exit;
}

// La app, en cambio, usa $_GET, que en PHP se queda con la ÚLTIMA aparición.
$n = query('SELECT titulo, texto FROM notas WHERE id = ?', [$_GET['id'] ?? 0])->fetch();
if (!$n) {
    lab_alert('err', 'Nota no encontrada.');
} else {
    echo '    <h2>' . e($n['titulo']) . "</h2>\n    <pre>" . e($n['texto']) . "</pre>\n";
}
lab_footer();
