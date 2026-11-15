<?php
require '/var/www/lab/lab.php';

lab_header('Widget embebible: ponlo en tu web y mándale avisos con postMessage.');
lab_nav(['/' => 'Widget']);
?>
    <div class="card"><b>Santo Domingo</b> · 31 °C · Soleado
      <div id="aviso" class="dim">Sin avisos.</div>
    </div>
    <p class="dim">Para integradores: desde la página que embebe el widget, manda el texto del aviso con
      <code>iframe.contentWindow.postMessage('Alerta de lluvia', '*')</code>.</p>
    <script>
      window.addEventListener('message', function (e) {
        // Acepta avisos de cualquier página que lo embeba.
        document.getElementById('aviso').innerHTML = e.data;
      });
    </script>
<?php
lab_footer();
