<?php
require '/var/www/lab/lab.php';

session_start();

// Recordamos el idioma elegido en la sesión del usuario.
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'es.php';

lab_header('16', 'Radio Online RD', 'La emisora del barrio, ahora en internet.', 'sesiones');
lab_nav(['?lang=es.php' => 'Español', '?lang=en.php' => 'English']);

if (preg_match('#://|data:#i', $lang)) {
    lab_alert('err', 'Wrappers no permitidos.');
} else {
    include 'languages/' . $lang;
}

lab_footer('consigue ejecución de comandos, lista / y lee el archivo /flag_*.txt.');
