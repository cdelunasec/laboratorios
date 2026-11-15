<?php
require '/var/www/lab/lab.php';

$me = require_login();
$mios = query('SELECT uuid, nombre FROM archivos WHERE user_id = ? ORDER BY creado', [$me['id']])->fetchAll();

lab_header('Tus archivos en la nube.');
?>
    <table class="data">
      <tr><th>Archivo</th></tr>
<?php foreach ($mios as $a): ?>
      <tr><td><a href="/archivo.php?id=<?= e($a['uuid']) ?>"><?= e($a['nombre']) ?></a></td></tr>
<?php endforeach; ?>
    </table>
    <p class="dim">Cada archivo tiene un identificador aleatorio imposible de adivinar.</p>
    <h2>Actividad reciente del equipo</h2>
    <ul id="actividad" class="dim"></ul>
    <script>
      fetch('/api/actividad.php').then(r => r.json()).then(items => {
        const ul = document.getElementById('actividad');
        for (const it of items) {
          const li = document.createElement('li');
          li.textContent = it.usuario + ' ' + it.accion;
          ul.appendChild(li);
        }
      });
    </script>
<?php
lab_footer();
