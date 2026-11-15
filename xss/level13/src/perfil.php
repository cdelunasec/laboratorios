<?php
require '/var/www/lab/lab.php';

$perfiles = datos_leer('perfiles');
$u = (string) ($_GET['u'] ?? '');
$p = $perfiles[$u] ?? null;

lab_header('Perfil de freelancer');
lab_nav(['/' => 'Crear perfil']);
if (!$p) {
    lab_alert('err', 'Ese perfil no existe.');
} else {
?>
    <h2><?= e($u) ?></h2>
    <p><?= e($p['bio']) ?></p>
    <p>Sitio web: <a id="web" href="<?= e($p['web']) ?>">visitar</a></p>
<?php
}
lab_footer();
