<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE pedidos (id INTEGER PRIMARY KEY, user_id INTEGER, producto TEXT, total INTEGER, nota TEXT)');

$productos = ['Arroz 10 lb', 'Habichuelas rojas', 'Salami', 'Café Santo Domingo', 'Aceite', 'Plátanos', 'Queso de freír', 'Leche en polvo'];
// Pedidos 1000-1199 repartidos entre María, Juan y Carlos.
for ($id = 1000; $id < 1200; $id++) {
    $uid = [2, 3, 4][random_int(0, 2)];
    query('INSERT INTO pedidos VALUES (?, ?, ?, ?, ?)', [$id, $uid, $productos[array_rand($productos)], random_int(150, 4000), '']);
}
// Garantiza que Carlos tenga al menos dos pedidos visibles.
query('UPDATE pedidos SET user_id = 4 WHERE id IN (1007, 1042)');
// Un único pedido del admin, en una posición al azar.
$admin = random_int(1050, 1199);
query('UPDATE pedidos SET user_id = 1, producto = ?, total = 0, nota = ? WHERE id = ?',
    ['Pedido interno', 'Código de descuento del 100%: ' . read_flag(), $admin]);
