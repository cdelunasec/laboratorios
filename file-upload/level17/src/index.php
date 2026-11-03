<?php
require '/var/www/lab/lab.php';

lab_header('17', 'SoporteTI', 'Adjunta una captura de tu problema. Por seguridad, el archivo se guarda con un nombre aleatorio que no mostramos.');

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!in_array($file['type'], $allowedTypes, true)) {
        lab_alert('err', 'Solo capturas de pantalla (imágenes).');
    } else {
        // Nombre "aleatorio" para que nadie adivine dónde quedó
        $dest = 'uploads/' . md5((string) time()) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Ticket #' . random_int(10000, 99999) . ' creado. Un técnico revisará tu captura pronto.');
    }
}

lab_upload_form('Captura de pantalla');
lab_footer();
