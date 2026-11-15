<?php
// Descarga del informe. El servidor es pequeño: no entrega archivos grandes de
// una vez, pero soporta descargas parciales (rangos de bytes).
const ARCHIVO = '/var/lib/labapp/informe-anual.txt';
const MAX_TROZO = 65536;

$size = filesize(ARCHIVO);
header('Accept-Ranges: bytes');
header('Content-Type: text/plain; charset=utf-8');

$range = $_SERVER['HTTP_RANGE'] ?? '';
if ($range === '') {
    http_response_code(413);
    echo "El archivo pesa $size bytes y este servidor no entrega más de " . MAX_TROZO . " bytes por petición.\n";
    exit;
}

if (!preg_match('/^bytes=(\d*)-(\d*)$/', trim($range), $m) || ($m[1] === '' && $m[2] === '')) {
    http_response_code(416);
    header("Content-Range: bytes */$size");
    echo "Rango inválido.\n";
    exit;
}

if ($m[1] === '') {
    // bytes=-N : los últimos N bytes
    $start = max(0, $size - (int) $m[2]);
    $end = $size - 1;
} else {
    $start = (int) $m[1];
    $end = $m[2] === '' ? $size - 1 : min((int) $m[2], $size - 1);
}

if ($start > $end || $start >= $size) {
    http_response_code(416);
    header("Content-Range: bytes */$size");
    echo "Rango fuera del archivo.\n";
    exit;
}

if ($end - $start + 1 > MAX_TROZO) {
    http_response_code(416);
    header("Content-Range: bytes */$size");
    echo "Ese rango pide " . ($end - $start + 1) . " bytes; el máximo por petición es " . MAX_TROZO . ".\n";
    exit;
}

$fh = fopen(ARCHIVO, 'rb');
fseek($fh, $start);
$data = fread($fh, $end - $start + 1);
fclose($fh);

http_response_code(206);
header("Content-Range: bytes $start-$end/$size");
header('Content-Length: ' . strlen($data));
echo $data;
