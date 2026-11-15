<?php
require '/var/www/lab/lab.php';

lab_header('Todo sobre la pelota invernal.');
lab_nav(['/' => 'Inicio']);

$q = (string) ($_GET['q'] ?? '');
// Quita cualquier etiqueta <script> o </script>.
$limpio = preg_replace('#</?script[^>]*>#i', '', $q);
?>
    <form class="stack" method="get">
      <input type="text" name="q" placeholder="Licey, Águilas, Escogido...">
      <button>Buscar temas</button>
    </form>
<?php
if ($q !== '') {
    echo "    <p>Temas sobre: <b>$limpio</b></p>\n";
}
lab_footer();
