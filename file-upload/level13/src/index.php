<?php
require '/var/www/lab/lab.php';

lab_header('13', 'GamerZone', 'Foro de gamers. Personaliza tu perfil con un avatar.');

$page = $_GET['page'] ?? 'home.php';
?>
    <nav class="menu">
      <a href="?page=home.php">Inicio</a>
      <a href="?page=avatar.php">Mi avatar</a>
      <a href="?page=reglas.php">Reglas</a>
    </nav>
<?php
include 'pages/' . $page;

lab_footer();
