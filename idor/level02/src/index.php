<?php
require '/var/www/lab/lab.php';

$me = require_login();

lab_header('Tu bandeja de entrada.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // El id del dueño de la bandeja viene del formulario.
    $msgs = query('SELECT de, texto FROM mensajes WHERE user_id = ? ORDER BY id', [$_POST['user_id'] ?? 0])->fetchAll();
    if (!$msgs) {
        lab_alert('err', 'No hay mensajes.');
    }
    foreach ($msgs as $m) {
        echo '    <div class="card"><b>' . e($m['de']) . '</b><br>' . e($m['texto']) . "</div>\n";
    }
}
?>
    <form method="post" class="stack">
      <input type="hidden" name="user_id" value="<?= e($me['id']) ?>">
      <button>Ver mis mensajes</button>
    </form>
<?php
lab_footer();
