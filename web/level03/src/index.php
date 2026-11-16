<?php
require '/var/www/lib/app.php';

cabecera('La comunidad dominicana para hablar de todo.');
?>
    <table class="data">
      <tr><th>Tema</th><th>Respuestas</th><th>Último mensaje</th></tr>
<?php foreach (array_reverse(temas(), true) as $t): $ult = end($t['mensajes']); ?>
      <tr>
        <td><a href="/tema.php?id=<?= (int) $t['id'] ?>"><?= e($t['titulo']) ?></a></td>
        <td><?= count($t['mensajes']) - 1 ?></td>
        <td class="muted"><?= e($ult['autor']) ?> · <?= e($ult['fecha']) ?></td>
      </tr>
<?php endforeach; ?>
    </table>
<?php
lab_footer();
