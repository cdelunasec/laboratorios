<?php
const LAB_APP = 'Intranet Ferretera';
const LAB_HINT = 'sin protección';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
<form method="POST" action="/admin/rol.php">
  <input name="usuario" value="maria">
  <select name="rol"><option>usuario</option><option>editor</option><option>admin</option></select>
  <button>Cambiar rol</button>
</form>
DOC;
