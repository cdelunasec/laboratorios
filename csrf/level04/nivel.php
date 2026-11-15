<?php
const LAB_APP = 'Supermercado Online';
const LAB_HINT = 'un token es un token';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
<form method="POST" action="/admin/rol.php">
  <input type="hidden" name="token" value="(token anti-CSRF)">
  <input name="usuario" value="maria">
  <select name="rol"><option>usuario</option><option>editor</option><option>admin</option></select>
  <button>Cambiar rol</button>
</form>
DOC;
