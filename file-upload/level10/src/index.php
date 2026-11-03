<?php
require '/var/www/lab/lab.php';

lab_header('10', 'BlogSpace', 'Tu blog personal. Sube imágenes y archivos para tus publicaciones.');

$blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'pgif', 'inc'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = basename($file['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (in_array($ext, $blocked, true)) {
        lab_alert('err', "Extensión .$ext bloqueada por seguridad.");
    } else {
        $dest = 'uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Archivo adjuntado:', '/' . $dest);
    }
}

lab_upload_form('Archivo para tu publicación');
lab_footer();
