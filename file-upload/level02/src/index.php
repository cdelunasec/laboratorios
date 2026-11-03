<?php
require '/var/www/lab/lab.php';

lab_header('02', 'GaleríaPro', 'Solo aceptamos imágenes JPG, PNG o GIF.');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } else {
        // La validación ya se hace en el navegador
        $dest = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Imagen publicada:', '/' . $dest);
    }
}

lab_upload_form('Imagen (jpg, png, gif)', ' accept=".jpg,.jpeg,.png,.gif"');
?>
<script>
document.querySelector('form.upload').addEventListener('submit', function (ev) {
    var name = document.getElementById('file').value.toLowerCase();
    var ok = ['.jpg', '.jpeg', '.png', '.gif'].some(function (ext) {
        return name.endsWith(ext);
    });
    if (!ok) {
        ev.preventDefault();
        alert('Formato no permitido. Solo JPG, PNG o GIF.');
    }
});
</script>
<?php
lab_footer();
