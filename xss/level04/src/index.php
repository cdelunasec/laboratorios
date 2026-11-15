<?php
require '/var/www/lab/lab.php';

lab_header('Medicamentos a domicilio.');
lab_nav(['/' => 'Inicio']);

$q = (string) ($_GET['q'] ?? '');
?>
    <form class="stack" method="get">
      <input type="text" name="q" placeholder="acetaminofén, loratadina...">
      <button>Buscar</button>
    </form>
    <p id="res" class="dim"></p>
    <script>
      // addslashes: "así nadie se sale de la cadena".
      var busqueda = '<?= addslashes($q) ?>';
      if (busqueda) {
        document.getElementById('res').textContent = 'Sin existencias de: ' + busqueda;
      }
    </script>
<?php
lab_footer();
