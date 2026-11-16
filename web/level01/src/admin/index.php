<?php
require '/var/www/lib/app.php';

$u = yo();
if (!$u || $u['rol'] !== 'admin') {
    http_response_code(403);
    cabecera('Administración', '', true);
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

$clientes = usuarios();
$imagenes = glob('/var/www/html/uploads/*') ?: [];

cabecera('Administración', 'Clientes, catálogo e imágenes de la tienda.');
if ($msg) {
    lab_alert($msg[0], $msg[1]);
}
?>
  <div class="cifras">
    <div class="cifra ld"><b><?= count($clientes) ?></b><span>cuentas registradas</span></div>
    <div class="cifra ld"><b><?= count(productos()) ?></b><span>productos en el catálogo</span></div>
    <div class="cifra ld"><b><?= count($imagenes) ?></b><span>imágenes subidas</span></div>
  </div>

  <div class="dos seccion">
    <section class="ld">
      <h2 style="margin-bottom: 10px">Clientes</h2>
      <table class="tabla">
        <tr><th>ID</th><th>Usuario</th><th>Correo</th><th>Rol</th></tr>
<?php foreach ($clientes as $c): ?>
        <tr><td class="mono"><?= (int) $c['id'] ?></td><td><?= e($c['usuario']) ?></td><td class="muted"><?= e($c['email']) ?></td><td><span class="chip<?= $c['rol'] === 'admin' ? '' : ' gris' ?>"><?= e($c['rol']) ?></span></td></tr>
<?php endforeach; ?>
      </table>
    </section>

    <section class="card ld">
      <h2>Imagen de producto</h2>
      <p class="muted chica">PNG, JPG o GIF. Queda publicada en <code>/uploads/</code>.</p>
      <form class="form" method="post" enctype="multipart/form-data">
        <input type="file" name="imagen" accept="image/*">
        <button>Subir imagen</button>
      </form>
<?php if ($imagenes): ?>
      <ul class="subidas">
<?php foreach ($imagenes as $img): ?>
        <li><a href="/uploads/<?= e(basename($img)) ?>"><?= e(basename($img)) ?></a></li>
<?php endforeach; ?>
      </ul>
<?php else: ?>
      <p class="muted chica" style="margin: 14px 0 0">Todavía no hay imágenes subidas.</p>
<?php endif; ?>
    </section>
  </div>
<?php
lab_footer();
