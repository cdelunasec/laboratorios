<?php
require '/var/www/lab/lab.php';
require '/var/www/lab/cuentas.php';

session_start();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = (string) ($_POST['usuario'] ?? '');
    $c = (string) ($_POST['clave'] ?? '');
    $d = datos_leer();
    if (isset($d['usuarios'][$u]) && password_verify($c, $d['usuarios'][$u])) {
        $_SESSION['usuario'] = $u;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
if (isset($_GET['salir'])) {
    $_SESSION = [];
}

lab_header('13', 'Correo Antillano', 'Webmail de la empresa.', 'el enlace lo arma tu petición');

if ($error) {
    lab_alert('err', $error);
}

$u = $_SESSION['usuario'] ?? null;
if ($u === 'admin') {
    lab_alert('ok', 'Bandeja del administrador.');
    lab_show_flag('Clave del servidor de correo');
    echo '    <p><a href="/?salir">Salir</a></p>' . "\n";
} elseif ($u !== null) {
    echo '    <p>Hola, <b>' . e($u) . '</b>. Tu bandeja está vacía. <a href="/?salir">Salir</a></p>' . "\n";
} else {
?>
    <form class="stack" method="post">
      <label>Usuario</label>
      <input type="text" name="usuario">
      <label>Contraseña</label>
      <input type="password" name="clave">
      <button>Entrar</button>
    </form>
    <p><a href="/olvide.php">¿Olvidaste tu contraseña?</a></p>
    <p class="dim">Cuenta de demostración: <code>demo</code> / <code>demo</code>.</p>
<?php
}
lab_footer('entra a la bandeja del usuario admin.');
