<?php
require '/var/www/lab/lab.php';

// Solo se llama desde el panel de admin... así que basta con estar logueado.
require_login_api();

$rows = query('SELECT id, username AS usuario, email AS correo, role AS rol, notas FROM users ORDER BY id')->fetchAll();
json_response($rows);
