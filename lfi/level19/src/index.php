<?php
// El tema se guarda en una cookie para recordarlo entre visitas.
if (!isset($_COOKIE['theme'])) {
    setcookie('theme', 'claro.php');
}
if (isset($_GET['theme']) && in_array($_GET['theme'], ['claro.php', 'oscuro.php'], true)) {
    setcookie('theme', $_GET['theme']);
    header('Location: /');
    exit;
}

require '/var/www/lab/lab.php';

lab_header('19', 'Blog de Viajes', 'Crónicas de un mochilero por la isla.', 'mira las galletas');
lab_nav(['?theme=claro.php' => 'Tema claro', '?theme=oscuro.php' => 'Tema oscuro']);

$theme = $_COOKIE['theme'] ?? 'claro.php';
include 'themes/' . $theme;
?>
<h2>Bahía de las Águilas</h2>
<p>Tres horas de carretera desde Barahona y vale cada minuto.</p>
<?php
lab_footer('lee el archivo /flag.txt del servidor.');
