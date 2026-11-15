<?php
require '/var/www/lab/lab.php';

lab_header('02', 'Liceo Duarte · Portal', 'Acceso de estudiantes.', '¿de dónde vienes?');

$clave = trim((string) @file_get_contents('/var/lib/labapp/clave'));
$enviada = (string) ($_POST['clave'] ?? '');

if ($clave !== '' && $enviada !== '' && hash_equals($clave, $enviada)) {
    echo '    <p><a href="/descargar.php">Descargar boletín</a></p>' . "\n";
} else {
    if ($enviada !== '') {
        lab_alert('err', 'Contraseña incorrecta.');
    }
?>
    <form class="stack" method="post">
      <label>Contraseña del portal</label>
      <input type="password" name="clave">
      <button>Entrar</button>
    </form>
<?php
}
lab_footer('descarga el boletín sin pasar por el portal (el portal pide una contraseña que no tienes).');
