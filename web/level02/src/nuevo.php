<?php
require '/var/www/lib/app.php';

$num = null;
$error = null;
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
    }
}

cabecera('Cuéntanos qué pasa.');
if ($error) {
    lab_alert('err', $error);
}
if ($num) {
    lab_alert('ok', "Ticket #$num creado. Guarda el número para consultar su estado.");
}
?>
    <form class="stack" method="post">
      <label>Tu nombre o empresa</label>
      <input type="text" name="nombre">
      <label>Asunto</label>
      <input type="text" name="asunto">
      <label>Describe el problema</label>
      <textarea name="mensaje" rows="6"></textarea>
      <button>Abrir ticket</button>
    </form>
<?php
lab_footer();
