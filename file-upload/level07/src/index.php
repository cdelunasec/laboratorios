<?php
require '/var/www/lab/lab.php';

lab_header('07', 'InmoFotos', 'Portal inmobiliario. Sube las fotos de tu propiedad.');

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = basename($file['name']);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!preg_match('/\.(jpe?g|png|gif)$/i', $name)) {
        lab_alert('err', 'Solo se permiten imágenes .jpg, .png o .gif');
    } elseif (!in_array($file['type'], $allowedTypes, true)) {
        lab_alert('err', 'El archivo no parece una imagen.');
    } else {
        $dest = 'uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Foto agregada al anuncio:', '/' . $dest);
    }
}

lab_upload_form('Foto de la propiedad (jpg, png, gif)');
lab_footer();
