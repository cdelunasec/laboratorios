<?php
require '/var/www/lab/lab.php';

lab_header('10', 'Importador de Productos', 'Importa la ficha de un producto desde la web del proveedor.', 'nombres internos');
?>
    <table class="data">
      <tr><th>Infraestructura</th><th>Estado</th></tr>
      <tr><td>web-01 (esta app)</td><td>operativo</td></tr>
      <tr><td>interno.corp:8080 (panel interno)</td><td>operativo</td></tr>
    </table>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL de la ficha', 'https://proveedor.com/producto/123', $url);

// Filtro "a prueba de todo" contra direcciones internas.
function host_bloqueado(string $host): bool
{
    $h = strtolower(trim($host, '[]'));
    return str_starts_with($h, 'localhost')
        || str_contains($h, ':')                               // cualquier IPv6
        || preg_match('/^(0|127)\./', $h)                      // 0.x y 127.x
        || preg_match('/^[0-9]+$/', $h)                        // 2130706433
        || preg_match('/^0x[0-9a-f]+$/', $h)                   // 0x7f000001
        || preg_match('/^[0-9.]+$/', $h) && !filter_var($h, FILTER_VALIDATE_IP)  // 127.1, octal...
        || filter_var($h, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
            && filter_var($h, FILTER_VALIDATE_IP);             // IPs privadas o reservadas
}

if ($url !== '') {
    $p = parse_url($url);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http', 'https'], true) || empty($p['host'])) {
        lab_alert('err', 'URL inválida (solo http/https).');
    } elseif (host_bloqueado($p['host'])) {
        lab_alert('err', 'Bloqueado: esa dirección es interna.');
    } else {
        [$code, $body, $err] = lab_curl($url);
        if ($err) {
            lab_alert('err', "Error: $err");
        } else {
            lab_alert('ok', "Respuesta $code");
            echo '    <pre>' . e(substr($body, 0, 4000)) . "</pre>\n";
        }
    }
}

lab_footer('lee la clave del panel interno.');
