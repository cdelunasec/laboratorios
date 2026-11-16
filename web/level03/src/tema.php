<?php
require '/var/www/lib/app.php';

$id = (int) ($_GET['id'] ?? 0);
$temas = temas();
if (!isset($temas[$id])) {
    http_response_code(404);
    cabecera('Ese tema no existe', 'Puede que lo hayan borrado o que el enlace esté mal.', true);
    echo '  <p class="ld"><a class="btn ghost" href="/">← Volver a los temas</a></p>' . "\n";
    lab_footer();
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apodo = trim((string) ($_POST['apodo'] ?? ''));
    $texto = trim((string) ($_POST['texto'] ?? ''));
    if (!preg_match('/^[\w.-]{2,20}$/u', $apodo) || $texto === '') {
        $error = 'Pon un apodo (2-20 letras o números) y un mensaje.';
    } else {
        $_SESSION['apodo'] = $apodo;
        $temas[$id]['mensajes'][] = ['autor' => $apodo, 'fecha' => date('Y-m-d H:i'), 'texto' => limpiar(mb_substr($texto, 0, 4000))];
        datos_guardar('temas', $temas);
        lab_redirect("/tema.php?id=$id");
    }
}

$t = $temas[$id];
cabecera('');
if ($error) {
    lab_alert('err', $error);
}
?>
  <div class="hilo-head ld">
    <div>
      <p class="muted chica" style="margin: 0 0 6px"><a href="/">Temas</a> / #<?= $id ?></p>
      <h1><?= e($t['titulo']) ?></h1>
    </div>
    <form class="reportar" method="post" action="/reportar.php">
      <input type="hidden" name="id" value="<?= $id ?>">
      <button>Reportar al moderador</button>
    </form>
  </div>

  <div class="mensajes">
<?php foreach ($t['mensajes'] as $i => $m): ?>
    <article class="mensaje<?= $i === 0 ? ' primero' : '' ?> ld">
      <?= avatar($m['autor'], true) ?>
      <div class="cuerpo">
        <div class="meta"><b><?= e($m['autor']) ?></b><?= $m['autor'] === 'moderador' ? ' <span class="chip">moderador</span>' : '' ?><span><?= e($m['fecha']) ?></span></div>
        <div class="texto"><?= $m['texto'] ?></div>
      </div>
    </article>
<?php endforeach; ?>
  </div>

  <section class="responder caja ld">
    <h2 style="font-size: 19px; margin-bottom: 14px">Responder</h2>
    <form class="form" method="post">
      <div class="campo"><label for="a">Apodo</label><input id="a" type="text" name="apodo" value="<?= e(apodo()) ?>"></div>
      <div class="campo"><label for="m">Mensaje (se permite HTML básico)</label><textarea id="m" name="texto" rows="4"></textarea></div>
      <button>Responder</button>
    </form>
  </section>
<?php
lab_footer();
