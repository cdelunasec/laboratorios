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

cabecera('Recupera tu cuenta', 'Te mandamos un enlace al correo para elegir otra contraseña.', true);
if ($enviado) {
    lab_alert('ok', 'Si la cuenta existe, te mandamos a su correo un enlace para cambiar la contraseña. Vence en 15 minutos.');
}
?>
  <div class="caja ld">
    <form class="form" method="post">
      <div class="campo"><label for="u">Usuario</label><input id="u" type="text" name="usuario" autocomplete="username"></div>
      <button>Enviar enlace</button>
    </form>
    <p class="extra"><a href="/login.php">← Volver a entrar</a></p>
  </div>
<?php
lab_footer();
