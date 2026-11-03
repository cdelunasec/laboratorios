<?php
require '/var/www/lab/lab.php';

lab_header('09', 'AvatarStudio', 'Generador de avatares. Validamos que tu imagen sea una imagen de verdad.');

$blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'phps'];

function is_real_image(string $path): bool
{
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)) {
        return false;
    }
    if (getimagesize($path) === false) {
        return false;
    }
    // Si GD no puede abrirla, no es una imagen válida
    return @imagecreatefromstring(file_get_contents($path)) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (in_array($ext, $blocked, true)) {
        lab_alert('err', "Extensión .$ext no permitida.");
    } elseif (!is_real_image($file['tmp_name'])) {
        lab_alert('err', 'La imagen está dañada o no es una imagen real.');
    } else {
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Avatar listo:', '/' . $dest);
    }
}

lab_upload_form('Imagen base para tu avatar');
lab_footer();
