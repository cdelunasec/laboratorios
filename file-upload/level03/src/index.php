<?php
require '/var/www/lab/lab.php';

lab_header('03', 'MemeHub', 'Sube tus memes. Nuestro servidor verifica que sean imágenes.');

$allowed = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!in_array($file['type'], $allowed, true)) {
        lab_alert('err', 'Tipo de archivo no permitido: ' . $file['type']);
    } else {
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Meme publicado:', '/' . $dest);
    }
}

lab_upload_form('Meme (jpg, png, gif)');
lab_footer();
