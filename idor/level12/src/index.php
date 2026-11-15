<?php
require '/var/www/lab/lab.php';

$me = require_login();

lab_header('Tus documentos oficiales.');
?>
    <form class="stack" onsubmit="buscar(event)">
      <label>Buscar ciudadano (para compartir documentos)</label>
      <input type="text" id="q" placeholder="nombre de usuario">
      <button>Buscar</button>
    </form>
    <div id="res"></div>
    <h2>Mis documentos</h2>
    <ul id="docs"></ul>
    <script>
      const YO = <?= json_encode($me['uuid']) ?>;
      fetch('/api/v2/documentos.php?usuario=' + YO).then(r => r.json()).then(ds => {
        const ul = document.getElementById('docs');
        for (const d of ds) {
          const li = document.createElement('li');
          li.textContent = d.titulo + ': ' + d.contenido;
          ul.appendChild(li);
        }
      });
      function buscar(ev) {
        ev.preventDefault();
        fetch('/api/v2/usuarios.php?q=' + encodeURIComponent(document.getElementById('q').value))
          .then(r => r.json()).then(us => {
            const res = document.getElementById('res');
            res.textContent = '';
            for (const u of us) {
              const d = document.createElement('div');
              d.className = 'card';
              const img = document.createElement('img');
              img.src = u.avatar; img.width = 32; img.style.verticalAlign = 'middle';
              d.appendChild(img);
              d.append(' ' + u.nombre + ' (' + u.usuario + ')');
              res.appendChild(d);
            }
          });
      }
    </script>
<?php
lab_footer();
