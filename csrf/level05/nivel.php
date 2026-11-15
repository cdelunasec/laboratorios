<?php
const LAB_APP = 'Clínica Dental';
const LAB_HINT = 'galleta contra galleta';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
<!-- "Double submit cookie": el token va en un campo y en la cookie csrf.
     El servidor solo comprueba que los dos coincidan. -->
<form method="POST" action="/admin/rol.php">
  <input type="hidden" name="token" value="(el mismo valor que la cookie csrf)">
  <input name="usuario" value="maria">
  <select name="rol"><option>usuario</option><option>editor</option><option>admin</option></select>
  <button>Cambiar rol</button>
</form>
DOC;
