<?php
require '/var/www/lab/lab.php';

lab_header('01', 'PerfilYa', 'Sube tu foto de perfil y compártela con tus amigos.');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } else {
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Foto subida:', '/' . $dest);
    }
}

lab_upload_form('Foto de perfil');
lab_footer();
