<?php
require '/var/www/lab/lab.php';

$method = $_SERVER['REQUEST_METHOD'];

// Compatibilidad con clientes viejos que no saben mandar PUT/DELETE.
foreach (['HTTP_X_HTTP_METHOD_OVERRIDE', 'HTTP_X_HTTP_METHOD', 'HTTP_X_METHOD_OVERRIDE'] as $h) {
    if (!empty($_SERVER[$h])) {
        $method = strtoupper(trim($_SERVER[$h]));
        break;
    }
}

$tareas = [
    ['id' => 1, 'texto' => 'Comprar pan', 'hecha' => false],
    ['id' => 2, 'texto' => 'Pagar la luz', 'hecha' => true],
];

switch ($method) {
    case 'GET':
        lab_json([
            'tareas' => $tareas,
            'endpoints' => [
                'GET /api/' => 'lista las tareas',
                'POST /api/' => 'crea una tarea (texto=...)',
                'PUT /api/' => 'exportación completa (uso interno)',
                'DELETE /api/' => 'borra todo (uso interno)',
            ],
        ]);
    case 'POST':
        lab_json(['ok' => true, 'creada' => ['id' => 3, 'texto' => (string) ($_POST['texto'] ?? ''), 'hecha' => false]], 201);
    case 'PUT':
        lab_json(['exportacion' => $tareas, 'clave_exportacion' => lab_flag()]);
    case 'DELETE':
        lab_json(['error' => 'Borrar todo está desactivado en producción.'], 403);
    default:
        lab_json(['error' => "Método $method no soportado"], 405);
}
