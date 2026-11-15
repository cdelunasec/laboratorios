<?php
require '/var/www/lab/lab.php';

// Versión en texto plano de una nota, para copiarla o integrarla.
$notas = datos_leer('notas');
$t = $notas[(int) ($_GET['id'] ?? 0)] ?? null;
if ($t === null) {
    http_response_code(404);
    exit;
}
header('Content-Type: text/plain; charset=utf-8');
echo html_entity_decode(strip_tags($t), ENT_QUOTES, 'UTF-8');
