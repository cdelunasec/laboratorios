<?php
require '/var/www/lab/lab.php';

require_login_api();

// El widget solo pinta usuario + acción, pero la API devuelve la fila entera.
$rows = query('SELECT u.username AS usuario, a.accion, a.archivo, a.creado
               FROM actividad a JOIN users u ON u.id = a.user_id ORDER BY a.creado DESC')->fetchAll();
json_response($rows);
