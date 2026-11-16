<?php
require '/var/www/lib/app.php';

$error = null;
$apodo = apodo();
$titulo = $texto = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apodo = trim((string) ($_POST['apodo'] ?? ''));
    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $texto = trim((string) ($_POST['texto'] ?? ''));
    if (!preg_match('/^[\w.-]{2,20}$/u', $apodo) || $titulo === '' || $texto === '') {
        $error = 'Pon un apodo (2-20 letras o números), un título y un mensaje.';
    } else {
        $_SESSION['apodo'] = $apodo;
        $t = temas();
        $id = max(array_keys($t)) + 1;
        $t[$id] = ['id' => $id, 'titulo' => mb_substr($titulo, 0, 120), 'mensajes' => [
            ['autor' => $apodo, 'fecha' => date('Y-m-d H:i'), 'texto' => limpiar(mb_substr($texto, 0, 4000))],
        ]];
        datos_guardar('temas', $t);
        lab_redirect("/tema.php?id=$id");
    }
}

cabecera('Nuevo tema', 'Pregunta, recomienda o cuenta. Se permite HTML básico para dar formato.', true);
if ($error) {
    lab_alert('err', $error);
}
?>
  <div class="caja ld">
    <form class="form" method="post">
      <div class="campo"><label for="a">Apodo</label><input id="a" type="text" name="apodo" value="<?= e($apodo) ?>"></div>
      <div class="campo"><label for="t">Título</label><input id="t" type="text" name="titulo" value="<?= e($titulo) ?>"></div>
      <div class="campo"><label for="m">Mensaje</label><textarea id="m" name="texto" rows="7"><?= e($texto) ?></textarea></div>
      <button>Publicar</button>
    </form>
  </div>
<?php
lab_footer();
