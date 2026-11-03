<?php
require '/var/www/lab/lab.php';

lab_header('18', 'MarketRD', 'Clasificados en línea. Sube la foto de lo que vendes. Corremos en Nginx, nada de trucos de Apache.');

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = basename($file['name']);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!preg_match('/^[\w-]+\.(jpe?g|png|gif)$/i', $name)) {
        lab_alert('err', 'Solo imágenes .jpg, .png o .gif con un nombre simple.');
    } elseif (!in_array($file['type'], $allowedTypes, true) || getimagesize($file['tmp_name']) === false) {
        lab_alert('err', 'El archivo no es una imagen válida.');
    } else {
        $dest = 'uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Anuncio publicado con la foto:', '/' . $dest);
    }
}

lab_upload_form('Foto del artículo');
lab_footer();
