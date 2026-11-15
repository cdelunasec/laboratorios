<?php
const LAB_APP = 'Banco Comunitario';
const LAB_HINT = 'mismo sitio, otro origen';
// "Con SameSite=Strict ya no hace falta token."
const LAB_SAMESITE = 'Strict';
const LAB_DOC = <<<'DOC'
<!-- Sin token: la cookie del admin es SameSite=Strict, "así que ningún otro
     sitio puede mandar peticiones con su sesión". -->
<form method="POST" action="/admin/rol.php">
  <input name="usuario" value="maria">
  <select name="rol"><option>usuario</option><option>editor</option><option>admin</option></select>
  <button>Cambiar rol</button>
</form>
DOC;
