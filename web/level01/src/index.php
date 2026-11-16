<?php
require '/var/www/lib/app.php';

cabecera('Útiles escolares y de oficina con entrega en todo Santo Domingo.');
?>
    <div class="grid">
<?php foreach (productos() as $p): ?>
      <div class="card">
        <h3><?= e($p['nombre']) ?></h3>
        <span class="precio">RD$ <?= number_format($p['precio']) ?></span>
      </div>
<?php endforeach; ?>
    </div>
    <p class="muted">¿Problemas con tu cuenta? <a href="/olvide.php">Recupera tu contraseña</a>.</p>
<?php
lab_footer();
