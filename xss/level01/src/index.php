<?php
require '/var/www/lab/lab.php';

lab_header('Busca entre cientos de recetas dominicanas.');
lab_nav(['/' => 'Inicio']);

$q = (string) ($_GET['q'] ?? '');
?>
    <form class="stack" method="get">
      <input type="text" name="q" placeholder="sancocho, mangú, habichuelas con dulce...">
      <button>Buscar</button>
    </form>
<?php
if ($q !== '') {
    echo "    <p>Resultados para: <b>$q</b></p>\n";
    echo "    <p class=\"dim\">No encontramos recetas con ese nombre.</p>\n";
}
lab_footer();
