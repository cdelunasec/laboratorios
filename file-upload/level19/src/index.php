<?php
require '/var/www/lab/lab.php';

lab_header('19', 'NotasSeguras', 'Comparte notas y archivos. Nuestro WAF revisa el contenido de cada archivo en busca de código malicioso.');

$blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phps'];

// Firmas que el WAF considera maliciosas
$waf = ['<?php', 'system', 'exec', 'passthru', 'popen', 'proc_open', 'eval', 'assert', 'base64_decode'];

function waf_match(string $content, array $rules): ?string
{
    foreach ($rules as $rule) {
        if (stripos($content, $rule) !== false) {
            return $rule;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (in_array($ext, $blocked, true)) {
        lab_alert('err', "Extensión .$ext bloqueada.");
    } elseif (($rule = waf_match(file_get_contents($file['tmp_name']), $waf)) !== null) {
        lab_alert('err', 'WAF: contenido malicioso detectado (' . $rule . ').');
    } else {
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Archivo compartido:', '/' . $dest);
    }
}

lab_upload_form('Archivo o nota');
lab_footer();
