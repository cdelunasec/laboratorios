<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE expedientes (id INTEGER PRIMARY KEY, user_id INTEGER, diagnostico TEXT, notas TEXT)');
$x = [
    [1, 1, 'Chequeo anual', 'Código de acceso a historiales: ' . read_flag()],
    [2, 2, 'Gripe', 'Reposo e hidratación.'],
    [3, 3, 'Esguince de tobillo', 'Hielo y vendaje.'],
    [4, 4, 'Chequeo general', 'Todo en orden. Bajar el café.'],
];
foreach ($x as $row) {
    query('INSERT INTO expedientes VALUES (?, ?, ?, ?)', $row);
}
