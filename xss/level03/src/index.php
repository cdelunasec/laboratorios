<?php
require '/var/www/lab/lab.php';

lab_header('Compra y vende de todo.');
lab_nav(['/' => 'Inicio']);

$q = (string) ($_GET['q'] ?? '');
// "Escapa" los signos < y > para que nadie meta etiquetas.
$seguro = str_replace(['<', '>'], ['&lt;', '&gt;'], $q);
?>
    <form class="stack" method="get">
      <input type="text" name="q" value="<?= $seguro ?>" placeholder="carro, nevera, solar...">
      <button>Buscar</button>
    </form>
<?php
if ($q !== '') {
    echo '    <p class="dim">0 anuncios para "' . $seguro . "\".</p>\n";
}
lab_footer();
