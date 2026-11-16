<?php
require '/var/www/lib/app.php';

$n = (string) ($_GET['n'] ?? '');
cabecera('Consulta el estado de tu ticket.');
?>
    <form class="stack" method="get">
      <label>Número de ticket</label>
      <input type="text" name="n" value="<?= e($n) ?>" placeholder="0001">
      <button>Consultar</button>
    </form>
<?php
if ($n !== '') {
    if (!preg_match('/^\d{4}$/', $n) || !is_file(SOPORTE . "/tickets/$n.txt")) {
        lab_alert('err', 'No encontramos ese ticket.');
    } else {
        echo '<pre>' . file_get_contents(SOPORTE . "/tickets/$n.txt") . '</pre>';
    }
}
lab_footer();
