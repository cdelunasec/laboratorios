<?php
$signatures = ["\xFF\xD8\xFF", "\x89PNG\r\n\x1A\n", 'GIF87a', 'GIF89a'];

function has_image_signature(string $path, array $signatures): bool
{
    $head = file_get_contents($path, false, null, 0, 8);
    foreach ($signatures as $magic) {
        if (strncmp($head, $magic, strlen($magic)) === 0) {
            return true;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = basename($file['name']);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!preg_match('/^[\w-]+\.(jpe?g|png|gif)$/i', $name)) {
        lab_alert('err', 'Solo imágenes .jpg, .png o .gif con un nombre simple.');
    } elseif (!has_image_signature($file['tmp_name'], $signatures) || getimagesize($file['tmp_name']) === false) {
        lab_alert('err', 'El archivo no es una imagen válida.');
    } else {
        $dest = 'uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Avatar actualizado:', '/' . $dest);
    }
}
?>
<h2>Mi avatar</h2>
<?php lab_upload_form('Avatar (jpg, png, gif)'); ?>
