<?php
require '/var/www/lab/lab.php';

// El mismo endpoint sirve a la web y a las integraciones: decide por Accept.
header('Vary: Accept');

$envio = [
    'guia' => 'RD-4471',
    'estado' => 'En tránsito',
    'origen' => 'Santiago',
    'destino' => 'Santo Domingo',
    'entrega_estimada' => 'mañana antes de las 6:00 pm',
    // Campo interno que la plantilla HTML nunca muestra.
    'nota_interna' => 'clave de integración: ' . lab_flag(),
];

$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
if (stripos($accept, 'application/json') !== false) {
    lab_json($envio);
}

lab_header('09', 'Envíos Caribe', 'Rastrea tu paquete.', 'negociación de contenido');
?>
    <table class="data">
      <tr><th>Guía</th><td><?= e($envio['guia']) ?></td></tr>
      <tr><th>Estado</th><td><?= e($envio['estado']) ?></td></tr>
      <tr><th>Ruta</th><td><?= e($envio['origen']) ?> → <?= e($envio['destino']) ?></td></tr>
      <tr><th>Entrega</th><td><?= e($envio['entrega_estimada']) ?></td></tr>
    </table>
    <p class="dim">¿Integras con nosotros? Esta misma URL también la consume nuestro sistema de integraciones.</p>
<?php
lab_footer('consigue la clave de integración.');
