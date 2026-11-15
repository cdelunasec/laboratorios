<?php
require '/var/www/lab/lab.php';

$perfiles = datos_leer('perfiles');
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = (string) ($_POST['usuario'] ?? '');
    $web = trim((string) ($_POST['web'] ?? ''));
    if (!preg_match('/^[a-z0-9_]{3,20}$/', $u)) {
        $error = 'Usuario: 3 a 20 letras minúsculas, números o guion bajo.';
    } elseif (!str_contains($web, 'http')) {
        $error = 'El sitio web debe ser una URL (http...).';
    } else {
        $perfiles[$u] = ['web' => $web, 'bio' => substr((string) ($_POST['bio'] ?? ''), 0, 200)];
        datos_guardar('perfiles', $perfiles);
        header('Location: /perfil.php?u=' . $u);
        exit;
    }
}

lab_header('Crea tu perfil. El admin verifica cada perfil abriendo tu sitio web.');
lab_nav(['/' => 'Crear perfil']);
if ($error) {
    lab_alert('err', $error);
}
?>
    <form class="stack" method="post">
      <input type="text" name="usuario" placeholder="usuario">
      <input type="text" name="web" placeholder="https://mi-portafolio.com">
      <textarea name="bio" rows="2" placeholder="Bio corta"></textarea>
      <button>Guardar perfil</button>
    </form>
    <p class="dim">Perfiles: <?php foreach (array_keys($perfiles) as $p) { echo '<a href="/perfil.php?u=' . e($p) . '">' . e($p) . '</a> '; } ?></p>
<?php
lab_footer();
