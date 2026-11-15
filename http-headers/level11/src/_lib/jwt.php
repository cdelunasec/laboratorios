<?php
// Mini librería JWT hecha en casa (HS256). Clave en /var/lib/labapp/jwt.key.

function b64url_enc(string $s): string
{
    return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}

function b64url_dec(string $s): string
{
    return (string) base64_decode(strtr($s, '-_', '+/'), false);
}

function jwt_key(): string
{
    return trim((string) file_get_contents('/var/lib/labapp/jwt.key'));
}

function jwt_sign(array $payload): string
{
    $h = b64url_enc(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $p = b64url_enc(json_encode($payload));
    $s = b64url_enc(hash_hmac('sha256', "$h.$p", jwt_key(), true));
    return "$h.$p.$s";
}

function jwt_verify(string $token): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }
    [$h, $p, $s] = $parts;
    $header = json_decode(b64url_dec($h), true);
    $payload = json_decode(b64url_dec($p), true);
    if (!is_array($header) || !is_array($payload)) {
        return null;
    }

    $alg = strtolower((string) ($header['alg'] ?? ''));
    if ($alg === 'none') {
        // Lo dejaron "para las pruebas del equipo de QA".
        return $payload;
    }
    if ($alg === 'hs256') {
        $ok = hash_equals(b64url_enc(hash_hmac('sha256', "$h.$p", jwt_key(), true)), $s);
        return $ok ? $payload : null;
    }
    return null;
}

function bearer_payload(): ?array
{
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if ($auth === '' && function_exists('getallheaders')) {
        $auth = array_change_key_case(getallheaders())['authorization'] ?? '';
    }
    if (!preg_match('/^Bearer\s+(\S+)$/i', $auth, $m)) {
        return null;
    }
    return jwt_verify($m[1]);
}
