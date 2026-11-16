<?php
require '/var/www/lib/app.php';

$nombre = strtolower(trim((string) ($_REQUEST['usuario'] ?? '')));
$token = (string) ($_REQUEST['token'] ?? '');
$u = usuarios();
$id = null;
foreach ($u as $i => $fila) {
    if ($fila['usuario'] === $nombre) {
        $id = $i;
    }
}

$valido = $id !== null && is_array($u[$id]['reset'])
    && hash_equals($u[$id]['reset']['token'], $token)
    && strtotime($u[$id]['reset']['expira']) > time();

$hecho = false;
$error = null;
if ($valido && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $clave = (string) ($_POST['clave'] ?? '');
    if (strlen($clave) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $u[$id]['clave'] = password_hash($clave, PASSWORD_DEFAULT);
        $u[$id]['reset'] = null;
        guardar_usuarios($u);
        $hecho = true;
    }
}

cabecera('Contraseña nueva', 'Elige una que no uses en otro sitio.', true);
if (!$valido && !$hecho) {
    lab_alert('err', 'El enlace no es válido o ya venció. Pide uno nuevo.');
    echo '  <p class="ld"><a class="btn ghost" href="/olvide.php">Pedir otro enlace</a></p>' . "\n";
} elseif ($hecho) {
    lab_alert('ok', 'Listo, ya puedes entrar con tu contraseña nueva.');
    echo '  <p class="ld"><a class="btn" href="/login.php">Entrar</a></p>' . "\n";
} else {
    if ($error) {
        lab_alert('err', $error);
    }
    ?>
  <div class="caja ld">
    <form class="form" method="post">
      <input type="hidden" name="usuario" value="<?= e($nombre) ?>">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="campo"><label for="c">Contraseña nueva para <?= e($nombre) ?></label><input id="c" type="password" name="clave" autocomplete="new-password"></div>
      <button>Cambiar contraseña</button>
    </form>
  </div>
    <?php
}
lab_footer();
