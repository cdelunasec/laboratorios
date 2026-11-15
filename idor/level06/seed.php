<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE archivos (uuid TEXT PRIMARY KEY, user_id INTEGER, nombre TEXT, contenido TEXT, creado INTEGER)');
db()->exec('CREATE TABLE actividad (id INTEGER PRIMARY KEY, user_id INTEGER, accion TEXT, archivo TEXT, creado INTEGER)');

$t = time() - 86400;
$a = [
    [1, 'respaldo-credenciales.txt', 'Clave maestra de la nube: ' . read_flag()],
    [2, 'fotos-boda.zip', '(binario)'],
    [3, 'presupuesto.xlsx', '(binario)'],
    [4, 'cv-carlos.pdf', 'Carlos Gómez — Técnico en redes.'],
    [4, 'lista-compra.txt', 'café, pan, salami'],
];
foreach ($a as $i => [$uid, $nombre, $c]) {
    $u = uuid4();
    query('INSERT INTO archivos VALUES (?, ?, ?, ?, ?)', [$u, $uid, $nombre, $c, $t + $i * 600]);
    query('INSERT INTO actividad (user_id, accion, archivo, creado) VALUES (?, ?, ?, ?)', [$uid, 'subió un archivo', $u, $t + $i * 600]);
}
