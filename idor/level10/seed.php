<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE movimientos (id INTEGER PRIMARY KEY, user_id INTEGER, fecha TEXT, concepto TEXT, monto INTEGER)');
db()->exec('CREATE TABLE exportaciones (id INTEGER PRIMARY KEY, user_id INTEGER, fecha TEXT)');

$mov = [
    [1, '2026-10-02', 'Transferencia interna', 0],
    [2, '2026-10-05', 'Depósito', 5000],
    [4, '2026-10-01', 'Depósito de nómina', 18000],
    [4, '2026-10-03', 'Pago préstamo', -3200],
    [4, '2026-10-10', 'Retiro cajero', -2000],
];
foreach ($mov as [$uid, $f, $c, $m]) {
    query('INSERT INTO movimientos (user_id, fecha, concepto, monto) VALUES (?, ?, ?, ?)', [$uid, $f, $c, $m]);
}

// El admin exportó sus datos hace unos días: el archivo sigue en /exports/.
$dias = random_int(2, 9);
$fecha = date('Ymd', time() - $dias * 86400);
query('INSERT INTO exportaciones (user_id, fecha) VALUES (1, ?)', [date('Y-m-d', time() - $dias * 86400)]);

$dir = '/var/www/html/exports';
@mkdir($dir, 0755, true);
file_put_contents("$dir/export_1_$fecha.csv",
    "fecha,concepto,monto\n2026-10-02,Transferencia interna,0\n#,Clave de la bóveda,\"" . read_flag() . "\"\n");
chmod("$dir/export_1_$fecha.csv", 0644);
chown($dir, 'www-data');
