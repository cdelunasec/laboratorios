<?php
require '/var/www/lib/app.php';

$u = yo();
if (!$u || $u['rol'] !== 'admin') {
    http_response_code(403);
    cabecera('Administración de la tienda.');
    lab_alert('err', 'Solo el administrador puede entrar aquí.');
    lab_footer();
    exit;
}

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen'])) {
    $f = $_FILES['imagen'];
    $permitidos = ['image/png', 'image/jpeg', 'image/gif'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $msg = ['err', 'No llegó ningún archivo.'];
    } elseif (!in_array($f['type'], $permitidos, true)) {
        $msg = ['err', 'Solo se permiten imágenes PNG, JPG o GIF.'];
    } else {
        $nombre = basename($f['name']);
        move_uploaded_file($f['tmp_name'], "/var/www/html/uploads/$nombre");
        $msg = ['ok', "Imagen subida: /uploads/$nombre"];
    }
}

cabecera('Administración de la tienda.');
if ($msg) {
    lab_alert($msg[0], $msg[1]);
}
?>
    <h2>Clientes</h2>
    <table class="data">
      <tr><th>ID</th><th>Usuario</th><th>Correo</th><th>Rol</th></tr>
<?php foreach (usuarios() as $c): ?>
      <tr><td><?= (int) $c['id'] ?></td><td><?= e($c['usuario']) ?></td><td><?= e($c['email']) ?></td><td><?= e($c['rol']) ?></td></tr>
<?php endforeach; ?>
    </table>
    <h2>Imagen de producto</h2>
    <form class="stack" method="post" enctype="multipart/form-data">
      <input type="file" name="imagen" accept="image/*">
      <button>Subir</button>
    </form>
    <h2>Imágenes subidas</h2>
    <ul>
<?php foreach (glob('/var/www/html/uploads/*') ?: [] as $img): ?>
      <li><a href="/uploads/<?= e(basename($img)) ?>"><?= e(basename($img)) ?></a></li>
<?php endforeach; ?>
    </ul>
<?php
lab_footer();
