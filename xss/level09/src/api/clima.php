<?php
// API JSONP del clima (para widgets viejos que no usan fetch).
header('Content-Type: application/javascript; charset=utf-8');
$cb = (string) ($_GET['callback'] ?? 'callback');
echo $cb . '(' . json_encode(['temp' => 30, 'estado' => 'Parcialmente nublado']) . ');';
