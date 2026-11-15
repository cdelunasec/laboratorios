<?php
// Servicio interno de inventario (lab local). Solo escucha en 127.0.0.1.
$flag = trim((string) file_get_contents('/var/lib/interno/flag'));
header('Content-Type: text/plain; charset=utf-8');
echo "Servicio de inventario v3\n\nArtículos: 1.284\nClave del servicio: $flag\n";
