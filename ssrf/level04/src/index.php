<?php
require '/var/www/lab/lab.php';

lab_header('04', 'Comprobador de Enlaces', '¿Tu enlace funciona? Lo probamos por ti.', '¿qué puerto?');
?>
    <p class="dim">El servicio de inventario corre en este mismo servidor, en algún puerto alto (entre 8000 y 8999). Nadie se acuerda de cuál.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL a comprobar', 'https://ejemplo.com/', $url);

if ($url !== '') {
    [$code, $body, $err] = lab_curl($url);
    if ($err) {
        lab_alert('err', "No funciona: $err");
    } else {
        lab_alert('ok', "Funciona (respuesta $code).");
        echo '    <pre>' . e(substr($body, 0, 4000)) . "</pre>\n";
    }
}

lab_footer('encuentra el servicio de inventario y lee su clave.');
