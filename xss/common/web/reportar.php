<?php
require '/var/www/lab/lab.php';

lab_header('¿Viste algo raro? Mándale el enlace al administrador y lo revisa enseguida.');
lab_nav(['/' => 'Inicio']);

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
        if ($ok) {
            lab_alert('ok', "El admin abrió $url");
        } else {
            lab_alert('err', "No se pudo avisar al admin ($msg). ¿Está corriendo el bot? ./lab.sh bot");
        }
    }
}
?>
    <form class="stack" method="post">
      <label>Ruta de este sitio (ej. <code>/?q=hola</code>) o URL del servidor de exploits</label>
      <input type="text" name="ruta" value="<?= e($ruta) ?>" placeholder="/?q=hola">
      <button>Avisar al admin</button>
    </form>
    <p class="dim">El admin entra a <code>http://<?= e(lab_host()) ?></code> con su sesión iniciada. Así ve él este sitio; tú lo ves en 127.0.0.1.</p>
<?php
lab_footer();
