<?php
require '/var/www/lab/lab.php';

lab_header('20', 'Fortaleza', 'Galería con todas las defensas: lista negra, verificación de imagen, WAF y PHP endurecido. Nginx bien configurado. Suerte.');

$blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'pgif', 'inc', 'htaccess'];
$signatures = ["\xFF\xD8\xFF", "\x89PNG\r\n\x1A\n", 'GIF87a', 'GIF89a'];
$waf = ['<?php', 'system', 'exec', 'passthru', 'popen', 'eval', 'assert'];

function starts_like_image(string $content, array $signatures): bool
{
    foreach ($signatures as $magic) {
        if (strncmp($content, $magic, strlen($magic)) === 0) {
            return true;
        }
    }
    return false;
}

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
    $name = basename($file['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $content = $file['error'] === UPLOAD_ERR_OK ? file_get_contents($file['tmp_name']) : '';

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif ($ext === '' || in_array($ext, $blocked, true)) {
        lab_alert('err', 'Extensión no permitida.');
    } elseif (!starts_like_image($content, $signatures)) {
        lab_alert('err', 'Solo se aceptan imágenes.');
    } elseif (($rule = waf_match($content, $waf)) !== null) {
        lab_alert('err', 'WAF: contenido malicioso detectado (' . $rule . ').');
    } else {
        $dest = 'uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Imagen agregada a la galería:', '/' . $dest);
    }
}

lab_upload_form('Imagen');
?>
    <p><a href="/uploads/">Ver la galería</a></p>
<?php
lab_footer();
