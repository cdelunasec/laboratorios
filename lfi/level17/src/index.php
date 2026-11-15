<?php
require '/var/www/lab/lab.php';

lab_header('17', 'Concurso de Fotos', 'Sube tu mejor foto de la playa.', 'archivos dentro de archivos');
lab_nav(['?page=inicio' => 'Inicio', '?page=participar' => 'Participar', '?page=bases' => 'Bases']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $f = $_FILES['foto'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if ($f['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir la foto.');
    } elseif (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        lab_alert('err', 'Solo se aceptan fotos .jpg o .png');
    } else {
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($f['name']));
        move_uploaded_file($f['tmp_name'], 'uploads/' . $name);
        lab_alert('ok', 'Foto recibida:', '/uploads/' . $name);
    }
}

$page = $_GET['page'] ?? 'inicio';

if (preg_match('#\.\./|php://|data:|file://|expect://|https?://|ftp://#i', $page)) {
    lab_alert('err', 'Página no permitida.');
} else {
    include $page . '.php';
}

lab_footer('consigue ejecución de comandos, lista / y lee el archivo /flag_*.txt.');
