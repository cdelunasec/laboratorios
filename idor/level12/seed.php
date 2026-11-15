<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE documentos (id INTEGER PRIMARY KEY, user_id INTEGER, titulo TEXT, contenido TEXT)');

// Ids de documentos dispersos para que no baste con enumerarlos a ciegas.
$usados = [];
$nuevo = function () use (&$usados) {
    do {
        $id = random_int(100000, 999999);
    } while (isset($usados[$id]));
    $usados[$id] = true;
    return $id;
};
$docs = [
    [1, 'Credenciales de producción', 'Clave del expediente maestro: ' . read_flag()],
    [1, 'Minuta de reunión', 'Se aprobó el presupuesto.'],
    [2, 'Cédula (copia)', 'María Pérez · 001-0000000-1'],
    [3, 'Acta de nacimiento', 'Juan Rodríguez · Santiago'],
    [4, 'Certificado de buena conducta', 'Carlos Gómez no tiene antecedentes.'],
    [4, 'Récord de notas', 'Ciberseguridad: 95'],
];
foreach ($docs as [$uid, $t, $c]) {
    query('INSERT INTO documentos VALUES (?, ?, ?, ?)', [$nuevo(), $uid, $t, $c]);
}

// Avatares públicos: el nombre del archivo es el id interno del usuario.
@mkdir('/var/www/html/avatars', 0755, true);
foreach (query('SELECT id, username FROM users')->fetchAll() as $u) {
    $l = strtoupper($u['username'][0]);
    file_put_contents("/var/www/html/avatars/{$u['id']}.svg",
        "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"64\" height=\"64\"><rect width=\"64\" height=\"64\" fill=\"#3fb950\"/>"
        . "<text x=\"32\" y=\"42\" font-size=\"30\" text-anchor=\"middle\" fill=\"#04130a\">$l</text></svg>");
}
