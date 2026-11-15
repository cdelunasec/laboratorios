<?php
require '/var/www/lab/lab.php';

// Si llegaste aquí es que Apache te dejó pasar.
lab_header('05', 'Guagua Express · Panel', 'Gestión de rutas.', 'verbos');
lab_alert('ok', 'Panel de rutas (método ' . $_SERVER['REQUEST_METHOD'] . ').');
lab_show_flag('Token del panel');
lab_footer('entra al panel de rutas.');
