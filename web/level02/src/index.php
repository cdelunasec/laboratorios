<?php
require '/var/www/lib/app.php';

cabecera('Soporte técnico para pymes: redes, impresoras, correo y lo que se rompa.');
?>
    <p>¿Algo no funciona? Abre un ticket y uno de nuestros técnicos te responde en menos de 24 horas.</p>
    <div class="grid">
      <div class="card"><h3>Redes y WiFi</h3><p class="muted">Configuración de routers, VLANs y cableado.</p></div>
      <div class="card"><h3>Correo</h3><p class="muted">Cuentas, Outlook, buzones llenos.</p></div>
      <div class="card"><h3>Impresoras</h3><p class="muted">Instalación, drivers y atascos.</p></div>
    </div>
    <p><a href="/nuevo.php">Abrir un ticket</a> · <a href="/ticket.php">Consultar el estado de mi ticket</a></p>
<?php
lab_footer();
