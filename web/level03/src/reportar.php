<?php
require '/var/www/lib/app.php';

$id = (int) ($_POST['id'] ?? 0);
if (!isset(temas()[$id])) {
    lab_redirect('/');
}

$r = datos_leer('reportes');
$r[] = ['tema' => $id, 'fecha' => date('Y-m-d H:i'), 'por' => apodo() ?: 'invitado'];
datos_guardar('reportes', $r);

// El moderador abre el tema reportado con su sesión.
$cookie = ['name' => MOD_COOKIE, 'value' => admin_token(), 'domain' => lab_host(), 'httpOnly' => false, 'sameSite' => 'Lax'];
[$ok, $msg] = bot_visitar('http://' . lab_host() . "/tema.php?id=$id", [$cookie]);

cabecera('Reporte enviado.');
lab_alert($ok ? 'ok' : 'err', $ok
    ? "Gracias. El moderador ya revisó el tema #$id."
    : "No se pudo avisar al moderador ($msg). ¿Está corriendo el bot? ./lab.sh bot");
echo '<p><a href="/tema.php?id=' . $id . '">← Volver al tema</a></p>';
lab_footer();
