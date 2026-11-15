<?php
const LAB_APP = 'Tienda de Celulares';
const LAB_HINT = 'pregunta y te lo dicen';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
// El frontend del admin pide su token a la API y luego manda el formulario.
const { token } = await (await fetch('/api/token.php', { credentials: 'include' })).json();
// <form method="POST" action="/admin/rol.php">
//   <input type="hidden" name="token" value="...token...">
//   <input name="usuario"> <select name="rol">...</select>
// </form>
DOC;
