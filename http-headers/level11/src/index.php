<?php
require '/var/www/lab/lab.php';

lab_header('11', 'Gimnasio Músculo RD', 'API de socios.', 'Authorization: Bearer');
?>
    <p>La app del gimnasio habla con esta API. Cuenta de prueba: <code>invitado</code> / <code>invitado</code>.</p>
    <table class="data">
      <tr><th>Endpoint</th><th>Qué hace</th></tr>
      <tr><td><code>POST /api/login.php</code></td><td>usuario=...&amp;clave=... → devuelve un token</td></tr>
      <tr><td><code>GET /api/perfil.php</code></td><td>tu perfil (requiere token)</td></tr>
      <tr><td><code>GET /api/admin.php</code></td><td>panel del dueño (requiere rol admin)</td></tr>
    </table>
    <p class="dim">El token se manda en la cabecera <code>Authorization: Bearer &lt;token&gt;</code>.</p>
<?php
lab_footer('entra al panel del dueño.');
