<?php
require '/var/www/lab/lab.php';

lab_header('Ahora la sesión es HttpOnly: JavaScript no la puede leer.');
lab_nav(['/' => 'Inicio']);

$q = (string) ($_GET['q'] ?? '');
?>
    <form class="stack" method="get">
      <input type="text" name="q" placeholder="Buscar en la ayuda">
      <button>Buscar</button>
    </form>
<?php
if ($q !== '') {
    echo "    <p>Sin resultados para: $q</p>\n";
}
lab_footer();
