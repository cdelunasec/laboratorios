<?php
require '/var/www/lab/lab.php';

lab_header('12', 'Tienda Local', 'Comparte un producto de nuestra tienda.', 'jefe final');
?>
    <p>Pega el enlace de un producto de <code>http://tienda.local/</code> y te generamos la tarjeta para redes sociales.</p>
    <p class="dim">Ofertas de socios: <a href="/ir.php?a=https://socios.ejemplo.com/">ver ofertas</a> · <a href="/ayuda.php">ayuda</a></p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('Enlace del producto', 'http://tienda.local/producto.php?id=1', $url);

if ($url !== '') {
    $p = parse_url($url);
    // Solo enlaces de nuestra propia tienda.
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http', 'https'], true)
        || strtolower($p['host'] ?? '') !== 'tienda.local' || isset($p['port']) || isset($p['user'])) {
        lab_alert('err', 'Solo se permiten enlaces de http://tienda.local/');
    } else {
        // Sigue redirecciones: algunos productos se movieron de URL.
        [$code, $body, $err] = lab_curl($url, CURLPROTO_HTTP | CURLPROTO_HTTPS, true);
        if ($err) {
            lab_alert('err', "Error: $err");
        } else {
            lab_alert('ok', "Tarjeta generada (respuesta $code). Vista previa del contenido:");
            echo '    <pre>' . e(substr(strip_tags($body), 0, 4000)) . "</pre>\n";
        }
    }
}

lab_footer('consigue las credenciales del rol de la instancia.');
