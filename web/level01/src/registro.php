<?php
require '/var/www/lib/app.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = strtolower(trim((string) ($_POST['usuario'] ?? '')));
    $email = trim((string) ($_POST['email'] ?? ''));
    $clave = (string) ($_POST['clave'] ?? '');
    if (!preg_match('/^[a-z0-9_]{3,20}$/', $nombre)) {
        $error = 'El usuario debe tener de 3 a 20 letras, números o guion bajo.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Correo inválido.';
    } elseif (strlen($clave) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif (buscar_usuario($nombre)) {
        $error = 'Ese usuario ya existe.';
    } else {
        $u = usuarios();
        $id = max(array_keys($u)) + 1;
        $u[$id] = ['id' => $id, 'usuario' => $nombre, 'email' => $email, 'rol' => 'cliente',
                   'clave' => password_hash($clave, PASSWORD_DEFAULT), 'creado' => date('Y-m-d'), 'reset' => null];
        guardar_usuarios($u);
        session_regenerate_id(true);
        $_SESSION['uid'] = $id;
        lab_redirect('/cuenta.php');
    }
}

cabecera('Crea tu cuenta y compra más rápido.');
if ($error) {
    lab_alert('err', $error);
}
?>
    <form class="stack" method="post">
      <label>Usuario</label>
      <input type="text" name="usuario">
      <label>Correo</label>
      <input type="text" name="email">
      <label>Contraseña</label>
      <input type="password" name="clave">
      <button>Crear cuenta</button>
    </form>
<?php
lab_footer();
