<?php
require '/var/www/lab/lab.php';

require_login();

// "Nadie va a adivinar una referencia así."
$ref = (string) base64_decode($_GET['ref'] ?? '', true);
$id = preg_match('/^documento-(\d+)$/', $ref, $m) ? (int) $m[1] : 0;
$d = query('SELECT titulo, contenido FROM documentos WHERE id = ?', [$id])->fetch();

lab_header('Documento');
if (!$d) {
    lab_alert('err', 'Referencia inválida.');
} else {
    echo '    <h2>' . e($d['titulo']) . "</h2>\n    <pre>" . e($d['contenido']) . "</pre>\n";
}
lab_footer();
