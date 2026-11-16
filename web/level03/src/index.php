<?php
require '/var/www/lib/app.php';

$temas = temas();
$mensajes = array_sum(array_map(fn ($t) => count($t['mensajes']), $temas));

cabecera('');
?>
  <section class="comunidad ld">
    <div>
      <h1>Aquí se habla <i>de todo.</i></h1>
      <p>La comunidad dominicana para preguntar, recomendar y quejarse de los apagones.</p>
    </div>
    <div class="cifras">
      <div><b><?= count($temas) ?></b>temas</div>
      <div><b><?= $mensajes ?></b>mensajes</div>
    </div>
  </section>

  <section class="seccion">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <h2 class="ld">Temas recientes</h2>
      <a class="btn ld" href="/nuevo.php">Nuevo tema</a>
    </div>
    <div class="temas">
<?php foreach (array_reverse($temas, true) as $t): $primero = $t['mensajes'][0]; $ult = end($t['mensajes']); ?>
      <a class="tema spot ld" href="/tema.php?id=<?= (int) $t['id'] ?>">
        <?= avatar($primero['autor'], true) ?>
        <div>
          <h3><?= e($t['titulo']) ?></h3>
          <div class="muted">último: <?= e($ult['autor']) ?> · <?= e($ult['fecha']) ?></div>
        </div>
        <span class="burbuja" title="respuestas"><?= count($t['mensajes']) - 1 ?></span>
      </a>
<?php endforeach; ?>
    </div>
  </section>
<?php
lab_footer();
