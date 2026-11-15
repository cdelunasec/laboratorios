<?php
require '/var/www/lab/lab.php';

lab_header('02', 'Liceo Duarte · Notas', 'Consulta de calificaciones del período.', '¿de dónde vienes?');
lab_nav(['/' => 'Inicio', '/portal/' => 'Portal estudiantil', '/descargar.php' => 'Descargar boletín']);
?>
    <p>Para descargar tu boletín entra primero al portal estudiantil y usa el enlace de allí.</p>
<?php
lab_footer('descarga el boletín sin pasar por el portal (el portal pide una contraseña que no tienes).');
