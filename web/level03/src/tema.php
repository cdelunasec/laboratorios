<?php
require '/var/www/lib/app.php';

$id = (int) ($_GET['id'] ?? 0);
$temas = temas();
if (!isset($temas[$id])) {
    http_response_code(404);
    cabecera('Ese tema no existe.');
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
cabecera($t['titulo']);
if ($error) {
    lab_alert('err', $error);
}
foreach ($t['mensajes'] as $m): ?>
    <div class="post">
      <div class="meta"><?= e($m['autor']) ?> · <?= e($m['fecha']) ?></div>
      <div><?= $m['texto'] ?></div>
    </div>
<?php endforeach; ?>
    <form class="row" method="post" action="/reportar.php">
      <input type="hidden" name="id" value="<?= $id ?>">
      <button>Reportar al moderador</button>
    </form>
    <h2>Responder</h2>
    <form class="stack" method="post">
      <label>Apodo</label>
      <input type="text" name="apodo" value="<?= e(apodo()) ?>">
      <label>Mensaje (se permite HTML básico)</label>
      <textarea name="texto" rows="4"></textarea>
      <button>Responder</button>
    </form>
<?php
lab_footer();
