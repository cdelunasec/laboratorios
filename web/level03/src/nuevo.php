<?php
require '/var/www/lib/app.php';

$error = null;
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

cabecera('Abre un tema nuevo.');
if ($error) {
    lab_alert('err', $error);
}
?>
    <form class="stack" method="post">
      <label>Apodo</label>
      <input type="text" name="apodo" value="<?= e(apodo()) ?>">
      <label>Título</label>
      <input type="text" name="titulo">
      <label>Mensaje (se permite HTML básico)</label>
      <textarea name="texto" rows="6"></textarea>
      <button>Publicar</button>
    </form>
<?php
lab_footer();
