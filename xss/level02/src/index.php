<?php
require '/var/www/lab/lab.php';

$libro = datos_leer('libro');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && trim((string) ($_POST['mensaje'] ?? '')) !== '') {
    $libro[] = ['nombre' => substr((string) ($_POST['nombre'] ?? 'Anónimo'), 0, 40), 'mensaje' => (string) $_POST['mensaje']];
    datos_guardar('libro', array_slice($libro, -20));
    header('Location: /');
    exit;
}

lab_header('Déjanos un mensaje. El admin revisa el libro cuando le avisan.');
lab_nav(['/' => 'Libro']);
?>
    <form class="stack" method="post">
      <input type="text" name="nombre" placeholder="Tu nombre">
      <textarea name="mensaje" rows="3" placeholder="Tu mensaje"></textarea>
      <button>Firmar el libro</button>
    </form>
<?php
foreach (array_reverse($libro) as $m) {
    // El nombre se escapa... el mensaje "admite formato".
    echo '    <div class="card"><b>' . e($m['nombre']) . '</b><br>' . $m['mensaje'] . "</div>\n";
}
lab_footer();
