<?php
// Servicio de metadatos de la instancia, al estilo IMDSv1 (lab local).
// Solo escucha en 127.0.0.1; el nombre metadata.nube.local apunta ahí.
$flag = trim((string) file_get_contents('/var/lib/interno/flag'));
$ruta = rtrim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
header('Content-Type: text/plain; charset=utf-8');

$rol = 'rol-app-web';
$arbol = [
    '' => "latest/\n",
    '/latest' => "meta-data/\n",
    '/latest/meta-data' => "ami-id\nhostname\ninstance-id\nlocal-ipv4\niam/\n",
    '/latest/meta-data/ami-id' => "ami-0c55b159cbfafe1f0\n",
    '/latest/meta-data/hostname' => "web-01.nube.local\n",
    '/latest/meta-data/instance-id' => "i-0a1b2c3d4e5f67890\n",
    '/latest/meta-data/local-ipv4' => "10.0.12.34\n",
    '/latest/meta-data/iam' => "security-credentials/\n",
    '/latest/meta-data/iam/security-credentials' => "$rol\n",
];

if (isset($arbol[$ruta])) {
    echo $arbol[$ruta];
} elseif ($ruta === "/latest/meta-data/iam/security-credentials/$rol") {
    header('Content-Type: application/json');
    echo json_encode([
        'Code' => 'Success',
        'Type' => 'AWS-HMAC',
        'AccessKeyId' => 'AKIANUBERD7EXAMPLE',
        'SecretAccessKey' => $flag,
        'Token' => base64_encode(random_bytes(24)),
        'Expiration' => gmdate('Y-m-d\TH:i:s\Z', time() + 3600),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
} else {
    http_response_code(404);
    echo "404 - Not Found\n";
}
