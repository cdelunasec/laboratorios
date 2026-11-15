<?php
require '/var/www/lab/lab.php';

lab_header('Crea una tarjeta y comparte el enlace.');
lab_nav(['/' => 'Inicio']);
?>
    <form class="stack" onsubmit="location.hash = encodeURIComponent(document.getElementById('n').value); return false;">
      <input type="text" id="n" placeholder="Nombre del cumpleañero">
      <button>Crear tarjeta</button>
    </form>
    <div class="card"><h2 id="tarjeta">¡Feliz cumpleaños!</h2></div>
    <script>
      // El nombre va en el fragmento (#...) para no guardarlo en el servidor.
      function pintar() {
        var n = decodeURIComponent(location.hash.slice(1));
        if (n) document.getElementById('tarjeta').innerHTML = '¡Feliz cumpleaños, ' + n + '!';
      }
      window.addEventListener('hashchange', pintar);
      pintar();
    </script>
<?php
lab_footer();
