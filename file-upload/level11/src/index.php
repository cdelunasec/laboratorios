<?php
require '/var/www/lab/lab.php';

lab_header('11', 'TareasITLA', 'Plataforma de entrega de tareas. Limpiamos el nombre de tu archivo automáticamente.');

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];

function sanitize_name(string $name): string
{
    // Quita cualquier rastro de extensiones ejecutables
    return str_ireplace(['php', 'phtml', 'phar'], '', $name);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = sanitize_name(basename($file['name']));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!in_array($file['type'], $allowedTypes, true)) {
        lab_alert('err', 'Formato no aceptado. Sube PDF, TXT o una imagen.');
    } elseif ($name === '' || $name[0] === '.') {
        lab_alert('err', 'Nombre de archivo inválido.');
    } else {
        $dest = 'uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Tarea entregada como:', '/' . $dest);
    }
}

lab_upload_form('Archivo de la tarea');
lab_footer();
