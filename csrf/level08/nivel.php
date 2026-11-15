<?php
const LAB_APP = 'Inmobiliaria del Este';
const LAB_HINT = 'sin referencia';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
<!-- Sin token. Se rechaza cualquier petición cuyo Referer sea de otro host. -->
<form method="POST" action="/admin/rol.php">
  <input name="usuario" value="maria">
  <select name="rol"><option>usuario</option><option>editor</option><option>admin</option></select>
  <button>Cambiar rol</button>
</form>
DOC;
