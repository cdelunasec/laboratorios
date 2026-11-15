<?php
require '/var/www/lab/lab.php';

lab_header('Mándale un enlace al admin. Lo abre enseguida, con su sesión iniciada.');

$ruta = trim((string) ($_POST['ruta'] ?? ''));
if ($ruta !== '') {
    if (str_starts_with($ruta, '/') && !str_starts_with($ruta, '//')) {
        $url = 'http://' . lab_host() . $ruta;
    } elseif (preg_match('#^http://exploit\.labs\.test/#', $ruta)) {
        $url = $ruta;
    } else {
        $url = null;
        lab_alert('err', 'Manda una ruta de este sitio (empieza por /) o una página del servidor de exploits (http://exploit.labs.test/...).');
    }
    if ($url !== null) {
        [$ok, $msg] = bot_visitar($url);
        lab_alert($ok ? 'ok' : 'err', $ok ? "El admin abrió $url" : "No se pudo avisar al admin ($msg). ¿Está corriendo el bot? ./lab.sh bot");
    }
}
?>
    <form class="stack" method="post">
      <label>URL del servidor de exploits (http://exploit.labs.test/p/...) o ruta de este sitio</label>
      <input type="text" name="ruta" value="<?= e($ruta) ?>" placeholder="http://exploit.labs.test/p/exploit.html">
      <button>Enviar al admin</button>
    </form>
<?php
lab_footer();
