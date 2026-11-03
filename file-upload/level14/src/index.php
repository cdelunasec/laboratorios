<?php
require '/var/www/lab/lab.php';

lab_header('14', 'SafeDrop', 'Cada archivo pasa por nuestro antivirus antes de publicarse.');

$allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

function antivirus_scan(string $path): void
{
    // El escáner externo tarda alrededor de un segundo por archivo
    sleep(1);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = basename($file['name']);
    $dest = 'uploads/' . $name;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } else {
        // Se guarda primero y se valida después
        move_uploaded_file($file['tmp_name'], $dest);
        antivirus_scan($dest);

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true) || getimagesize($dest) === false) {
            unlink($dest);
            lab_alert('err', 'Archivo rechazado por el análisis de seguridad y eliminado.');
        } else {
            lab_alert('ok', 'Archivo limpio y publicado:', '/' . $dest);
        }
    }
}

lab_upload_form('Archivo (jpg, png, gif)');
lab_footer();
