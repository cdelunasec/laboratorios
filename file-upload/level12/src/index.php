<?php
require '/var/www/lab/lab.php';

lab_header('12', 'FotoAlbum', 'Organiza tus fotos por álbum. La carpeta de subidas no ejecuta código, así que aceptamos cualquier archivo.');

$albums = ['vacaciones', 'familia', 'mascotas'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $album = $_POST['album'] ?? 'vacaciones';
    $dir = 'uploads/' . $album;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif (!is_dir($dir)) {
        lab_alert('err', 'Ese álbum no existe.');
    } else {
        $dest = $dir . '/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        lab_alert('ok', 'Foto guardada en:', '/' . $dest);
    }
}
?>
    <form method="post" enctype="multipart/form-data" class="upload">
      <label for="album">Álbum</label>
      <select name="album" id="album">
<?php foreach ($albums as $a): ?>
        <option value="<?= e($a) ?>"><?= e(ucfirst($a)) ?></option>
<?php endforeach; ?>
      </select>
      <label for="file">Foto</label>
      <input type="file" name="file" id="file" required>
      <button type="submit">Subir</button>
    </form>
<?php
lab_footer();
