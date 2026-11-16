<?php
require '/var/www/lib/app.php';

if (!es_moderador()) {
    http_response_code(403);
    cabecera('Panel de moderación.');
    lab_alert('err', 'Solo para moderadores.');
    lab_footer();
    exit;
}

$msg = null;
$respuesta = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim((string) ($_POST['url'] ?? ''));
    $p = parse_url($url);
    $host = strtolower((string) ($p['host'] ?? ''));
    if (!in_array($p['scheme'] ?? '', ['http', 'https'], true) || $host === '') {
        $msg = ['err', 'Solo URLs http o https.'];
    } elseif (preg_match('/localhost|127\.0\.0\.1|0\.0\.0\.0|::1/', $host)) {
        $msg = ['err', 'No se permiten direcciones internas.'];
    } else {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'ForoQuisqueya-Importador/1.0',
        ]);
        $cuerpo = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $tipo = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $err = curl_error($ch);
        curl_close($ch);
        $ext = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif'][strtolower(explode(';', $tipo)[0])] ?? null;
        if ($cuerpo === false) {
            $msg = ['err', "No se pudo descargar: $err"];
        } elseif ($ext === null) {
            $msg = ['err', "Eso no es una imagen (HTTP $code, " . ($tipo ?: 'sin tipo') . '). Esto fue lo que respondió:'];
            $respuesta = substr((string) $cuerpo, 0, 4000);
        } else {
            $nombre = bin2hex(random_bytes(8)) . ".$ext";
            file_put_contents("/var/www/html/uploads/portadas/$nombre", $cuerpo);
            $msg = ['ok', "Portada importada: /uploads/portadas/$nombre"];
        }
    }
}

cabecera('Panel de moderación.');
if ($msg) {
    lab_alert($msg[0], $msg[1]);
}
if ($respuesta !== null) {
    echo '<pre>' . e($respuesta) . "</pre>\n";
}
?>
    <span class="badge">moderador</span>
    <h2>Reportes recientes</h2>
    <table class="data">
      <tr><th>Tema</th><th>Fecha</th><th>Reportado por</th></tr>
<?php foreach (array_slice(array_reverse(datos_leer('reportes')), 0, 10) as $r): ?>
      <tr><td><a href="/tema.php?id=<?= (int) $r['tema'] ?>">#<?= (int) $r['tema'] ?></a></td><td><?= e($r['fecha']) ?></td><td><?= e($r['por']) ?></td></tr>
<?php endforeach; ?>
    </table>
    <h2>Importar portada desde una URL</h2>
    <form class="stack" method="post">
      <input type="text" name="url" placeholder="https://ejemplo.com/portada.png">
      <button>Importar</button>
    </form>
    <h2>Notas</h2>
    <ul>
      <li>Si el foro se pone lento, revisen el panel de diagnóstico del servidor (puerto 8080, solo local).</li>
      <li>Nada de importar imágenes de direcciones internas: sistemas ya lo bloqueó.</li>
    </ul>
<?php
lab_footer();
