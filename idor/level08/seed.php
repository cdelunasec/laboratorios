<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE notas (id INTEGER PRIMARY KEY, user_id INTEGER, titulo TEXT, texto TEXT)');
$n = [
    [1, 1, 'Accesos', 'Clave del panel: ' . read_flag()],
    [2, 2, 'Receta', 'Moro de guandules con coco.'],
    [3, 3, 'Pendientes', 'Llevar el carro al taller.'],
    [20, 4, 'Lista', 'Pagar el internet.'],
    [21, 4, 'Ideas', 'Montar un lab de redes en casa.'],
];
foreach ($n as $row) {
    query('INSERT INTO notas VALUES (?, ?, ?, ?)', $row);
}
