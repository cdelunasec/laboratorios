<?php
require '/var/www/lab/lab.php';

header("Content-Security-Policy: script-src 'self'; object-src 'none'; base-uri 'none'");

$notas = datos_leer('notas');

// Filtro: fuera manejadores de eventos, javascript: y marcos.
function filtrar(string $t): string
{
    $t = preg_replace('/on[a-z]+\s*=/i', 'bloqueado=', $t);
    $t = preg_replace('/javascript:/i', 'bloqueado:', $t);
    return preg_replace('#</?(iframe|object|embed|base)[^>]*>#i', '', $t);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && trim((string) ($_POST['texto'] ?? '')) !== '') {
    $id = count($notas) + 1;
    $notas[$id] = filtrar((string) $_POST['texto']);
    datos_guardar('notas', $notas);
    header('Location: /?nueva=' . $id);
    exit;
}

lab_header('Notas compartidas del equipo. Con CSP estricta y filtro de HTML.');
lab_nav(['/' => 'Notas']);
if (isset($_GET['nueva'])) {
    lab_alert('ok', 'Nota #' . (int) $_GET['nueva'] . ' guardada. Texto plano en /api/nota.php?id=' . (int) $_GET['nueva']);
}
?>
    <form class="stack" method="post">
      <textarea name="texto" rows="3" placeholder="Escribe una nota (se permite HTML básico)"></textarea>
      <button>Guardar nota</button>
    </form>
<?php
foreach (array_reverse($notas, true) as $id => $t) {
    echo '    <div class="card"><span class="dim">#' . (int) $id . '</span> ' . $t . "</div>\n";
}
lab_footer();
