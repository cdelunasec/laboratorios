<?php
require '/var/www/lab/lab.php';

lab_header('20', 'Fortaleza', 'Galería pública.');

$images = array_filter(scandir('.'), fn ($f) => preg_match('/\.(jpe?g|png|gif)$/i', $f));
?>
    <p><a href="/">Volver</a></p>
<?php if (!$images): ?>
    <p>Todavía no hay imágenes.</p>
<?php else: ?>
    <ul class="gallery">
<?php foreach ($images as $img): ?>
      <li><img src="<?= e($img) ?>" alt="<?= e($img) ?>"></li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php
lab_footer();
