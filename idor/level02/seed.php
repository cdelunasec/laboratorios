<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE mensajes (id INTEGER PRIMARY KEY, user_id INTEGER, de TEXT, texto TEXT)');
$m = [
    [1, 'Soporte', 'Bienvenido al sistema, admin. Tu clave de respaldo: ' . read_flag()],
    [2, 'María', '¿Me aprobaste las vacaciones?'],
    [4, 'Recursos Humanos', 'Carlos, recuerda subir tu constancia.'],
    [4, 'Juan', '¿Vamos a almorzar al colmado?'],
    [3, 'Carlos', 'Dale, a las 12.'],
];
foreach ($m as [$uid, $de, $t]) {
    query('INSERT INTO mensajes (user_id, de, texto) VALUES (?, ?, ?)', [$uid, $de, $t]);
}
