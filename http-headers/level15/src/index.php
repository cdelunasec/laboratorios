<?php
require '/var/www/lab/lab.php';

const MAX_INTENTOS = 5;
const DIR_INTENTOS = '/var/lib/labapp/intentos';

// IP del cliente "detrás del balanceador".
$ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'])[0]);
$archivo = DIR_INTENTOS . '/' . md5($ip);
$fallos = (int) @file_get_contents($archivo);

lab_header('15', 'Cajero Virtual BanReservitas', 'Cuenta de nómina ···· 4471.', 'límite de intentos');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = (string) ($_POST['pin'] ?? '');
    if ($fallos >= MAX_INTENTOS) {
        lab_alert('err', "Demasiados intentos desde $ip. Esta IP está bloqueada.");
    } elseif (hash_equals(trim((string) file_get_contents('/var/lib/labapp/pin')), $pin)) {
        lab_alert('ok', 'PIN correcto.');
        lab_show_flag('Saldo disponible');
    } else {
        $fallos++;
        file_put_contents($archivo, (string) $fallos, LOCK_EX);
        lab_alert('err', 'PIN incorrecto.');
    }
}

$quedan = max(0, MAX_INTENTOS - $fallos);
?>
    <form class="stack" method="post">
      <label>PIN de 4 dígitos</label>
      <input type="password" name="pin" maxlength="4" inputmode="numeric">
      <button>Entrar</button>
    </form>
    <p class="dim">Intentos restantes para <?= e($ip) ?>: <?= $quedan ?> de <?= MAX_INTENTOS ?>.</p>
<?php
lab_footer('entra a la cuenta (el PIN es de 4 dígitos y cambia en cada reinicio del nivel).');
