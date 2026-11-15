<?php
require '/var/www/lab/lab.php';

lab_header('06', "Tareas Pa' Ya", 'Lista de pendientes con API REST.', 'el WAF solo deja GET y POST');
lab_nav(['/' => 'Inicio', '/api/' => 'API']);
?>
    <p>Usa la API en <code>/api/</code>. Por seguridad, el firewall solo deja pasar <code>GET</code> y <code>POST</code>.</p>
    <p class="dim">¿Tu cliente es viejo y no sabe mandar otros métodos? La API trae compatibilidad para eso.</p>
<?php
lab_footer('consigue la exportación completa de la API.');
