<?php
require '/var/www/lab/lab.php';

lab_header('09', 'Conversor de Imágenes', 'Convierte una imagen de internet a otro formato.', 'la nube sabe quién eres');
?>
    <p class="dim">Este servicio corre en una instancia de <b>NubeRD</b>. Como en las nubes grandes, cada instancia tiene un servicio de metadatos (solo accesible desde la propia instancia) en <code>http://metadata.nube.local:8775/latest/meta-data/</code>.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL de la imagen', 'https://ejemplo.com/foto.png', $url);

if ($url !== '') {
    [$code, $body, $err] = lab_curl($url);
    if ($err) {
        lab_alert('err', "Error: $err");
    } elseif (!str_starts_with($body, "\x89PNG") && !str_starts_with($body, "\xFF\xD8")) {
        lab_alert('err', "Eso no es una imagen (respuesta $code). Contenido recibido:");
        echo '    <pre>' . e(substr($body, 0, 4000)) . "</pre>\n";
    } else {
        lab_alert('ok', 'Imagen convertida.');
    }
}

lab_footer('consigue las credenciales del rol de la instancia.');
