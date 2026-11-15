<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE facturas (id INTEGER PRIMARY KEY, ref TEXT UNIQUE, user_id INTEGER, periodo TEXT, monto INTEGER, detalle TEXT)');
$f = [
    [1, 1, 'Octubre 2026', 0, 'Cuenta de servicio interno. Clave de facturación: ' . read_flag()],
    [2, 2, 'Octubre 2026', 1840, '312 kWh'],
    [3, 3, 'Octubre 2026', 2210, '365 kWh'],
    [12, 4, 'Septiembre 2026', 1520, '268 kWh'],
    [13, 4, 'Octubre 2026', 1675, '290 kWh'],
];
foreach ($f as [$id, $uid, $per, $monto, $det]) {
    // La referencia pública de la factura es el md5 del número.
    query('INSERT INTO facturas VALUES (?, ?, ?, ?, ?, ?)', [$id, md5((string) $id), $uid, $per, $monto, $det]);
}
