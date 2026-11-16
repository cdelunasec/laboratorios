<?php
require '/var/www/lib/app.php';

exigir_oficina();
cabecera('Consola de agentes', 'Herramientas internas del equipo de soporte.');
$lista = tickets();
?>
  <section class="ld">
    <table class="tabla">
      <tr><th>Archivo</th><th>Cliente</th><th>Asunto</th><th>Estado</th><th></th></tr>
<?php foreach ($lista as $t): $d = datos_ticket($t); ?>
      <tr>
        <td><code><?= e($t) ?></code></td>
        <td><?= $d['Cliente'] ?? '' ?></td>
        <td class="muted"><?= $d['Asunto'] ?? '' ?></td>
        <td><span class="chip<?= ($d['Estado'] ?? '') === 'abierto' ? '' : ' gris' ?>"><?= e($d['Estado'] ?? '') ?></span></td>
        <td><a href="/interno/ver.php?archivo=<?= e(urlencode($t)) ?>">Ver</a></td>
      </tr>
<?php endforeach; ?>
    </table>
<?php if (!$lista): ?>
    <div class="card plano vacio"><strong>No hay tickets todavía.</strong>Cuando un cliente abra uno, aparece aquí.</div>
<?php endif; ?>
  </section>

  <section class="seccion card plano ld">
    <h2 style="font-size: 16px; margin-bottom: 14px">Notas del equipo</h2>
    <ul class="notas">
      <li>El visor abre los tickets de <span class="ruta"><?= e(SOPORTE) ?>/tickets/</span></li>
      <li>La bitácora de accesos del portal está en <span class="ruta"><?= e(SOPORTE) ?>/logs/acceso.log</span>. Para verla, pídesela a sistemas.</li>
      <li>Desde que el balanceador manda la IP real del cliente, esta sección ya no se ve desde fuera.</li>
    </ul>
  </section>
<?php
lab_footer();
