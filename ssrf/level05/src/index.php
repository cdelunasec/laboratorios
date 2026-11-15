<?php
require '/var/www/lab/lab.php';

lab_header('05', 'Galería de Productos', 'Muestra la imagen de un producto desde nuestro CDN.', 'lo que va antes de la arroba');
?>
    <p class="dim">Solo se aceptan imágenes de nuestro CDN: <code>http://cdn.tienda.local/...</code>. El panel interno de administración está en el puerto 8080 del servidor.</p>
<?php
$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL de la imagen', 'http://cdn.tienda.local/productos/1.jpg', $url);

if ($url !== '') {
    if (!str_starts_with($url, 'http://cdn.tienda.local')) {
        lab_alert('err', 'Solo se permiten URLs de http://cdn.tienda.local');
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
