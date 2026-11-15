<?php
// CORS: se confía en cualquier subdominio de labs.test ("son todos nuestros").
function cors(): void
{
    $o = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');
    if (preg_match('#^http://[a-z0-9-]+\.labs\.test$#', $o)) {
        header('Access-Control-Allow-Origin: ' . $o);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Vary: Origin');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
