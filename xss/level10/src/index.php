<?php
require '/var/www/lab/lab.php';

// CSP con nonce: solo corren los <script> que traigan el nonce de esta respuesta.
$nonce = base64_encode(random_bytes(16));
header("Content-Security-Policy: script-src 'nonce-$nonce'");

lab_header('Encuentra el colmado más cercano. Protegido con CSP y nonce.');
lab_nav(['/' => 'Inicio']);

$zona = (string) ($_GET['zona'] ?? '');
?>
    <form class="stack" method="get">
      <input type="text" name="zona" placeholder="Los Mina, Villa Juana, Gazcue...">
      <button>Buscar</button>
    </form>
<?php if ($zona !== ''): ?>
    <p>Colmados en <b><?= $zona ?></b>:</p>
<?php endif; ?>
    <ul id="lista" class="dim"></ul>
    <script nonce="<?= $nonce ?>" src="app.js"></script>
<?php
lab_footer();
