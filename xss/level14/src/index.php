<?php
require '/var/www/lab/lab.php';

lab_header('Traduce palabras al taíno. Ahora todo pasa por htmlspecialchars.');
lab_nav(['/' => 'Inicio']);

$p = (string) ($_GET['p'] ?? '');
?>
    <form class="stack" method="get">
      <input type="text" name="p" placeholder="casa, huracán, hamaca...">
      <button>Traducir</button>
    </form>
<?php
if ($p !== '') {
    // Escapado... y luego se "arreglan" las palabras con tildes que llegan codificadas.
    $salida = urldecode(htmlspecialchars($p));
    echo "    <p>No tenemos traducción para: <b>$salida</b></p>\n";
}
lab_footer();
