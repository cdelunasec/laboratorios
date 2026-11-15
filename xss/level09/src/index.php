<?php
require '/var/www/lab/lab.php';

// CSP: solo se ejecutan scripts servidos por este mismo sitio.
header("Content-Security-Policy: script-src 'self'");

lab_header('El tiempo en tu ciudad. Protegido con Content-Security-Policy.');
lab_nav(['/' => 'Inicio']);

$ciudad = (string) ($_GET['ciudad'] ?? '');
?>
    <form class="stack" method="get">
      <input type="text" name="ciudad" placeholder="Santiago, La Romana, Higüey...">
      <button>Ver pronóstico</button>
    </form>
<?php if ($ciudad !== ''): ?>
    <p>Pronóstico para <b><?= $ciudad ?></b>:</p>
<?php endif; ?>
    <div id="clima" class="card dim">Cargando…</div>
    <script src="/js/clima.js"></script>
    <script src="/api/clima.php?callback=mostrarClima"></script>
<?php
lab_footer();
