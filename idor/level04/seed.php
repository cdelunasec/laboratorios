<?php
require __DIR__ . '/lab.php';

seed_users();
db()->exec('CREATE TABLE documentos (id INTEGER PRIMARY KEY, user_id INTEGER, titulo TEXT, contenido TEXT)');
$d = [
    [1, 1, 'Poder notarial (confidencial)', 'Clave del archivo notarial: ' . read_flag()],
    [2, 2, 'Contrato de alquiler', 'María Pérez alquila el apartamento 3B...'],
    [3, 3, 'Acta de compraventa', 'Juan Rodríguez vende un carro Corolla 2009...'],
    [7, 4, 'Declaración jurada', 'Yo, Carlos Gómez, declaro que...'],
    [8, 4, 'Carta de no antecedentes', 'Certificamos que Carlos Gómez...'],
];
foreach ($d as $row) {
    query('INSERT INTO documentos VALUES (?, ?, ?, ?)', $row);
}
