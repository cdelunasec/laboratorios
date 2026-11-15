<?php
// Redirección para enlaces de afiliados. Sin validar el destino.
$a = (string) ($_GET['a'] ?? '/');
header('Location: ' . $a, true, 302);
echo "Redirigiendo a $a\n";
