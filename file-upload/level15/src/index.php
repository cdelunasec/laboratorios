<?php
require '/var/www/lab/lab.php';

lab_header('15', 'PrintShop', 'Imprime tus fotos. Reprocesamos cada imagen para quitar metadatos y contenido oculto.');

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = basename($file['name']);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!preg_match('/\.(jpe?g|png|gif)$/i', $name) || !in_array($file['type'], $allowedTypes, true)) {
        lab_alert('err', 'Solo fotos JPG, PNG o GIF.');
    } elseif (($img = @imagecreatefromstring(file_get_contents($file['tmp_name']))) === false) {
        lab_alert('err', 'No pudimos procesar la imagen.');
    } else {
        $dest = 'uploads/' . $name;
        $type = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if ($type === 'png') {
            imagepng($img, $dest);
        } elseif ($type === 'gif') {
            // Los GIF pueden ser animados; reprocesarlos los rompe,
            // así que guardamos el original tal cual.
            move_uploaded_file($file['tmp_name'], $dest);
        } else {
            imagejpeg($img, $dest);
        }
        imagedestroy($img);
        lab_alert('ok', 'Foto lista para imprimir:', '/' . $dest);
    }
}

lab_upload_form('Foto (jpg, png o gif)');
lab_footer();
