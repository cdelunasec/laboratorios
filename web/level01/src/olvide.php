<?php
require '/var/www/lib/app.php';

$enviado = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = strtolower(trim((string) ($_POST['usuario'] ?? '')));
    $u = usuarios();
    foreach ($u as $id => $fila) {
        if ($fila['usuario'] === $nombre) {
            $u[$id]['reset'] = ['token' => bin2hex(random_bytes(16)), 'expira' => date('c', time() + 900)];
            guardar_usuarios($u);
            // Aquí se mandaría el correo con el enlace /restablecer.php?usuario=...&token=...
            break;
        }
    }
    $enviado = true;
}

cabecera('Recupera el acceso a tu cuenta.');
if ($enviado) {
    lab_alert('ok', 'Si la cuenta existe, te mandamos a su correo un enlace para cambiar la contraseña. Vence en 15 minutos.');
}
?>
    <form class="stack" method="post">
      <label>Usuario</label>
      <input type="text" name="usuario">
      <button>Enviar enlace</button>
    </form>
<?php
lab_footer();
