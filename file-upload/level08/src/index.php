<?php
require '/var/www/lab/lab.php';

lab_header('08', 'RecetaFácil', 'Comparte la foto de tu plato. Revisamos el contenido real del archivo, no solo el nombre.');

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

// Firmas (magic bytes) de los formatos permitidos
$signatures = [
    "\xFF\xD8\xFF" => 'jpeg',
    "\x89PNG\r\n\x1A\n" => 'png',
    'GIF87a' => 'gif',
    'GIF89a' => 'gif',
];

function detect_image(string $path, array $signatures): ?string
{
    $head = file_get_contents($path, false, null, 0, 8);
    foreach ($signatures as $magic => $format) {
        if (strncmp($head, $magic, strlen($magic)) === 0) {
            return $format;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!in_array($file['type'], $allowedTypes, true)) {
        lab_alert('err', 'Tipo de archivo no permitido.');
    } elseif (detect_image($file['tmp_name'], $signatures) === null) {
        lab_alert('err', 'El contenido del archivo no es una imagen válida.');
    } else {
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Receta publicada:', '/' . $dest);
    }
}

lab_upload_form('Foto del plato');
lab_footer();
