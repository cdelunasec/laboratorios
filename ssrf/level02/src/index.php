<?php
require '/var/www/lab/lab.php';

lab_header('02', 'Vista Previa 2.0', 'Ahora con protección contra accesos internos.', 'localhost tiene muchos nombres');
?>
    <p class="dim">El panel interno sigue en el puerto 8080, pero ya no se puede pedir por <code>localhost</code> ni por <code>127.0.0.1</code>.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL a previsualizar', 'https://ejemplo.com/', $url);

if ($url !== '') {
    if (stripos($url, 'localhost') !== false || str_contains($url, '127.0.0.1')) {
        lab_alert('err', 'Bloqueado: no se permiten direcciones internas.');
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
