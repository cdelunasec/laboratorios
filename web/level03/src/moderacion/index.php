<?php
require '/var/www/lib/app.php';

if (!es_moderador()) {
    http_response_code(403);
    cabecera('Moderación', '', true);
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

cabecera('Moderación', 'Reportes de la comunidad y herramientas del equipo.');
if ($msg) {
    lab_alert($msg[0], $msg[1]);
}
if ($respuesta !== null) {
    echo '<div class="respuesta ld"><pre>' . e($respuesta) . "</pre></div>\n";
}
$reportes = array_slice(array_reverse(datos_leer('reportes')), 0, 10);
?>
  <div class="mod-grid">
    <section class="ld">
      <h2 style="margin-bottom: 10px">Reportes recientes</h2>
<?php if ($reportes): ?>
      <table class="tabla">
        <tr><th>Tema</th><th>Fecha</th><th>Reportado por</th></tr>
<?php foreach ($reportes as $r): ?>
        <tr><td><a href="/tema.php?id=<?= (int) $r['tema'] ?>">#<?= (int) $r['tema'] ?></a></td><td class="muted"><?= e($r['fecha']) ?></td><td><?= e($r['por']) ?></td></tr>
<?php endforeach; ?>
      </table>
<?php else: ?>
      <div class="card plano vacio"><strong>Sin reportes.</strong>Cuando alguien reporte un tema, aparece aquí.</div>
<?php endif; ?>
    </section>

    <div style="display: grid; gap: 18px">
      <section class="caja ld">
        <h2 style="margin-bottom: 6px">Importar portada desde una URL</h2>
        <p class="muted chica">La imagen se descarga al servidor y queda en <code>/uploads/portadas/</code>.</p>
        <form class="form" method="post">
          <input type="text" name="url" placeholder="https://ejemplo.com/portada.png">
          <button>Importar</button>
        </form>
      </section>
      <section class="card plano ld">
        <h2 style="font-size: 16px; margin-bottom: 12px">Notas</h2>
        <ul class="notas">
          <li>Si el foro se pone lento, revisen el panel de diagnóstico del servidor (puerto 8080, solo local).</li>
          <li>Nada de importar imágenes de direcciones internas: sistemas ya lo bloqueó.</li>
        </ul>
      </section>
    </div>
  </div>
<?php
lab_footer();
