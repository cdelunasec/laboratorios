<?php
require '/var/www/lib/app.php';

$n = (string) ($_GET['n'] ?? '');
cabecera('Consultar un ticket', 'Escribe el número que te dimos al abrirlo.', true);
?>
  <div class="caja ld">
    <form class="form" method="get">
      <div class="campo"><label for="n">Número de ticket</label><input id="n" type="text" name="n" value="<?= e($n) ?>" placeholder="0001" inputmode="numeric"></div>
      <button>Consultar</button>
    </form>
  </div>
<?php
if ($n !== '') {
    if (!preg_match('/^\d{4}$/', $n) || !is_file(SOPORTE . "/tickets/$n.txt")) {
        echo '<div class="ticket">';
        lab_alert('err', 'No encontramos ese ticket. Revisa el número: son cuatro dígitos, como 0001.');
        echo '</div>';
    } else {
        $d = datos_ticket("$n.txt");
        $abierto = ($d['Estado'] ?? '') === 'abierto';
        echo '<section class="ticket ld"><div class="ticket-head"><h2>Ticket #' . e($n) . '</h2>'
            . '<span class="chip' . ($abierto ? '' : ' gris') . '">' . ($abierto ? 'abierto' : 'cerrado') . '</span></div>';
        echo '<pre>' . file_get_contents(SOPORTE . "/tickets/$n.txt") . '</pre></section>';
    }
}
lab_footer();
