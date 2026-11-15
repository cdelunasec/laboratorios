<?php
require '/var/www/lab/lab.php';

lab_header('06', 'Acortador de Enlaces', 'Te decimos a dónde lleva un enlace corto.', 'sígueme');
?>
    <p class="dim">Para evitar abusos, la URL no puede apuntar a este servidor (se resuelve el nombre y se bloquea 127.0.0.0/8). El panel interno sigue en el puerto 8080.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('Enlace corto', 'https://bit.ly/abc123', $url);

function destino_permitido(string $url): ?string
{
    $p = parse_url($url);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http', 'https'], true) || empty($p['host'])) {
        return 'URL inválida (solo http/https).';
    }
    $host = trim($p['host'], '[]');
    $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return "No se pudo resolver $host.";
    }
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return 'Solo se aceptan destinos IPv4.';
    }
    if (str_starts_with($ip, '127.') || str_starts_with($ip, '0.')) {
        return "Bloqueado: $host apunta a este servidor ($ip).";
    }
    return null;
}

if ($url !== '') {
    $error = destino_permitido($url);
    if ($error) {
        lab_alert('err', $error);
    } else {
        // Seguimos las redirecciones: así sabemos a dónde lleva el enlace corto.
        [$code, $body, $err] = lab_curl($url, CURLPROTO_HTTP | CURLPROTO_HTTPS, true);
        if ($err) {
            lab_alert('err', "Error: $err");
        } else {
            lab_alert('ok', "Destino final: respuesta $code");
            echo '    <pre>' . e(substr($body, 0, 4000)) . "</pre>\n";
        }
    }
}

lab_footer('lee la clave del panel interno.');
