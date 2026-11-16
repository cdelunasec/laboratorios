<?php
require '/var/www/lib/app.php';

$num = null;
$error = null;
$nombre = $asunto = $mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $asunto = trim((string) ($_POST['asunto'] ?? ''));
    $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
    if ($nombre === '' || $asunto === '' || $mensaje === '') {
        $error = 'Llena todos los campos.';
    } else {
        $num = sprintf('%04d', count(tickets()) + 1);
        // Se guarda ya escapado: así se puede mostrar tal cual en el portal.
        $texto = "Ticket #$num\nFecha: " . date('Y-m-d H:i') . "\nCliente: " . e($nombre)
            . "\nAsunto: " . e($asunto) . "\nEstado: abierto\n\n" . e($mensaje) . "\n";
        file_put_contents(SOPORTE . "/tickets/$num.txt", $texto);
        $nombre = $asunto = $mensaje = '';
    }
}

cabecera('Abrir un ticket', 'Cuéntanos qué pasa. Mientras más detalle, más rápido lo resolvemos.');
if ($error) {
    lab_alert('err', $error);
}
if ($num) {
    lab_alert('ok', "Ticket #$num creado. Guarda el número para consultar su estado.");
}
?>
  <div class="con-lado">
    <div class="caja ld">
      <form class="form" method="post">
        <div class="campo"><label for="n">Tu nombre o empresa</label><input id="n" type="text" name="nombre" value="<?= e($nombre) ?>"></div>
        <div class="campo"><label for="a">Asunto</label><input id="a" type="text" name="asunto" value="<?= e($asunto) ?>" placeholder="Ej.: la impresora de caja no imprime"></div>
        <div class="campo"><label for="m">Describe el problema</label><textarea id="m" name="mensaje" rows="6"><?= e($mensaje) ?></textarea></div>
        <button>Abrir ticket</button>
      </form>
    </div>
    <aside class="card plano ld">
      <h2 style="font-size: 16px; margin-bottom: 16px">Qué pasa después</h2>
      <ol class="pasos">
        <li><span><b>Recibimos tu ticket</b>Te damos un número para darle seguimiento.</span></li>
        <li><span><b>Un técnico lo revisa</b>En menos de 24 horas, casi siempre antes.</span></li>
        <li><span><b>Lo resolvemos</b>Remoto si se puede; si no, pasamos por allá.</span></li>
      </ol>
    </aside>
  </div>
<?php
lab_footer();
