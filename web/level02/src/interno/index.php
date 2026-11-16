<?php
require '/var/www/lib/app.php';

exigir_oficina();
cabecera('Herramientas internas del equipo de soporte.');
?>
    <span class="badge">red de la oficina</span>
    <h2>Tickets</h2>
    <table class="data">
      <tr><th>Archivo</th><th></th></tr>
<?php foreach (tickets() as $t): ?>
      <tr><td><code><?= e($t) ?></code></td><td><a href="/interno/ver.php?archivo=<?= e(urlencode($t)) ?>">Ver</a></td></tr>
<?php endforeach; ?>
    </table>
<?php if (!tickets()): ?>
    <p class="muted">No hay tickets todavía.</p>
<?php endif; ?>
    <h2>Notas del equipo</h2>
    <ul>
      <li>El visor abre los tickets de <code><?= e(SOPORTE) ?>/tickets/</code>.</li>
      <li>La bitácora de accesos del portal está en <code><?= e(SOPORTE) ?>/logs/acceso.log</code>. Para verla, pídesela a sistemas.</li>
      <li>Desde que el balanceador manda la IP real del cliente, esta sección ya no se ve desde fuera.</li>
    </ul>
<?php
lab_footer();
