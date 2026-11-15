<?php
require '/var/www/lab/lab.php';

lab_header('Comparte tu obra: se permite HTML para dar formato.');
lab_nav(['/' => 'Inicio']);

$obra = (string) ($_GET['obra'] ?? '');
$bloqueado = preg_match('/script/i', $obra)          // nada de <script> ni javascript:
          || preg_match('/on[a-z]+\s*=/i', $obra);   // nada de onerror=, onload=...
?>
    <form class="stack" method="get">
      <textarea name="obra" rows="3" placeholder="<b>Mi cuadro</b> <i>óleo sobre tela</i>"></textarea>
      <button>Vista previa</button>
    </form>
<?php
if ($obra !== '') {
    if ($bloqueado) {
        lab_alert('err', 'Contenido peligroso detectado.');
    } else {
        echo "    <div class=\"card\">$obra</div>\n";
    }
}
lab_footer();
