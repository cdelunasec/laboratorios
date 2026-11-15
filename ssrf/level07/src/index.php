<?php
require '/var/www/lab/lab.php';

lab_header('07', 'Probador de Webhooks', 'Comprueba que tu webhook responde.', 'solo te dice si funcionó');
?>
    <p class="dim">Para los integradores: el servicio interno de licencias (puerto 8080 de este servidor) valida claves con <code>/verificar?prefijo=...</code> y responde 200 si la clave de licencia empieza así, o 404 si no.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL del webhook', 'https://mi-app.com/webhook', $url);

if ($url !== '') {
    [$code, , $err] = lab_curl($url);
    // Por privacidad nunca mostramos lo que responde el webhook.
    if ($err) {
        lab_alert('err', 'El webhook no respondió.');
    } else {
        lab_alert($code === 200 ? 'ok' : 'err', "El webhook respondió con código $code.");
    }
}

lab_footer('saca la clave de licencia completa del servicio interno.');
