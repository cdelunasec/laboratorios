<?php
require '/var/www/lab/lab.php';

lab_header('05', 'CloudBox', 'Almacenamiento en la nube. Bloqueamos todas las extensiones peligrosas.');

$blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (in_array($ext, $blocked, true)) {
        lab_alert('err', "Extensión .$ext bloqueada.");
    } else {
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Archivo guardado:', '/' . $dest);
    }
}

lab_upload_form('Archivo');
lab_footer();
