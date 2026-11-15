<?php
require '/var/www/lab/lab.php';

lab_header('08', 'Monitor de Servicios', 'Consulta el estado de cualquier servicio de la red.', 'otro protocolo, otro verbo');
?>
    <p class="dim">Soporta <code>http</code>, <code>https</code> y <code>gopher</code> (para equipos viejos). El panel interno está en el puerto 8080 del servidor.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL del servicio', 'http://servicio.local/estado', $url);

if ($url !== '') {
    [$code, $body, $err] = lab_curl($url, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_GOPHER);
    if ($err && $body === '') {
        lab_alert('err', "Error: $err");
    } else {
        lab_alert('ok', 'Respuesta del servicio:');
        echo '    <pre>' . e(substr($body, 0, 4000)) . "</pre>\n";
    }
}

lab_footer('consigue la exportación del panel interno.');
