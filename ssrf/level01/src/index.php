<?php
require '/var/www/lab/lab.php';

lab_header('01', 'Vista Previa', 'Pega un enlace y te mostramos qué hay.', 'el servidor pide por ti');
?>
    <p class="dim">Nuestro panel interno de administración vive en el puerto 8080 de este mismo servidor. Desde fuera no se puede entrar.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL a previsualizar', 'https://ejemplo.com/', $url);

if ($url !== '') {
    [$code, $body, $err] = lab_curl($url);
    if ($err) {
        lab_alert('err', "Error: $err");
    } else {
        lab_alert('ok', "Respuesta $code");
        echo '    <pre>' . e(substr($body, 0, 4000)) . "</pre>\n";
    }
}

lab_footer('lee la clave del panel interno.');
