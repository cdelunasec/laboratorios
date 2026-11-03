<?php
require '/var/www/lab/lab.php';

lab_header('04', 'DocuShare', 'Comparte documentos con tu equipo. Los scripts están bloqueados.');

$blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'phps'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (in_array($ext, $blocked, true)) {
        lab_alert('err', "Los archivos .$ext están bloqueados por seguridad.");
    } else {
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Documento compartido:', '/' . $dest);
    }
}

lab_upload_form('Documento');
lab_footer();
