<?php
require '/var/www/lab/lab.php';

require_login_api();

$p = query('SELECT p.id, u.username AS cliente, p.producto, p.total, p.nota
            FROM pedidos p JOIN users u ON u.id = p.user_id WHERE p.id = ?', [$_GET['id'] ?? 0])->fetch();

if (!$p) {
    json_response(['error' => 'Pedido no encontrado'], 404);
}
json_response($p);
