<?php
const LAB_APP = 'Agencia de Viajes';
const LAB_HINT = 'si no viene, no se revisa';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
<form method="POST" action="/admin/rol.php">
  <input type="hidden" name="token" value="(token anti-CSRF del admin)">
  <input name="usuario" value="maria">
  <select name="rol"><option>usuario</option><option>editor</option><option>admin</option></select>
  <button>Cambiar rol</button>
</form>
DOC;
