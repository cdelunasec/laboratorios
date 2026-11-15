<?php
const LAB_APP = 'Periódico Digital';
const LAB_HINT = 'el Referer delata';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
<!-- Sin token: "comprobamos que la petición venga de nuestro propio sitio"
     mirando la cabecera Referer. -->
<form method="POST" action="/admin/rol.php">
  <input name="usuario" value="maria">
  <select name="rol"><option>usuario</option><option>editor</option><option>admin</option></select>
  <button>Cambiar rol</button>
</form>
DOC;
