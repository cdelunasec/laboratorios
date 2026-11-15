<?php
require '/var/www/lab/lab.php';

lab_header('05', 'Guagua Express', 'Boletos de autobús a todo el país.', 'verbos');
lab_nav(['/' => 'Inicio', '/admin/panel.php' => 'Panel de rutas']);
?>
    <p>El panel de rutas lo protege el propio servidor web: solo la red interna puede entrar.</p>
<?php
lab_footer('entra al panel de rutas.');
