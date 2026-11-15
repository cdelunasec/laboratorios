<?php
require '/var/www/lab/lab.php';

const MAX = 45;
$socios = datos_leer('socios');
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = (string) ($_POST['nombre'] ?? '');
    if ($n === '' || mb_strlen($n) > MAX) {
        $error = 'El nombre debe tener entre 1 y ' . MAX . ' caracteres.';
    } else {
        $socios[] = $n;
        datos_guardar('socios', array_slice($socios, -30));
        header('Location: /');
        exit;
    }
}

lab_header('Inscríbete en el directorio. El admin lo revisa cuando le avisan.');
lab_nav(['/' => 'Directorio']);
if ($error) {
    lab_alert('err', $error);
}
?>
    <form class="stack" method="post">
      <input type="text" name="nombre" maxlength="<?= MAX ?>" placeholder="Tu nombre (máx. <?= MAX ?> caracteres)">
      <button>Inscribirme</button>
    </form>
    <ul>
<?php foreach (array_reverse($socios) as $s): ?>
      <li><?= $s ?></li>
<?php endforeach; ?>
    </ul>
<?php
lab_footer();
