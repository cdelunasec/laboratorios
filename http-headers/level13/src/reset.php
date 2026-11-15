<?php
require '/var/www/lab/lab.php';
require '/var/www/lab/cuentas.php';

lab_header('13', 'Correo Antillano', 'Nueva contraseña.', 'el enlace lo arma tu petición');

$token = (string) ($_REQUEST['token'] ?? '');
$d = datos_leer();
$t = $d['tokens'][$token] ?? null;

if ($t === null || $t['vence'] < time()) {
    lab_alert('err', 'Enlace inválido o vencido.');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['nueva'] ?? '') !== '') {
    $d['usuarios'][$t['usuario']] = password_hash((string) $_POST['nueva'], PASSWORD_DEFAULT);
    unset($d['tokens'][$token]);
    datos_guardar($d);
    lab_alert('ok', 'Contraseña cambiada para ' . $t['usuario'] . '.');
    echo '    <p><a href="/">Entrar</a></p>' . "\n";
} else {
?>
    <form class="stack" method="post">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <label>Nueva contraseña para <?= e($t['usuario']) ?></label>
      <input type="password" name="nueva">
      <button>Cambiar</button>
    </form>
<?php
}
lab_footer('entra a la bandeja del usuario admin.');
