<?php
const LAB_APP = 'Plataforma de Cursos';
const LAB_HINT = 'no es un formulario... ¿o sí?';
const LAB_SAMESITE = 'Lax';
const LAB_DOC = <<<'DOC'
// La app del admin usa una API JSON (sin token: "los formularios HTML no
// pueden mandar JSON, así que no hace falta").
fetch('/admin/rol.php', {
  method: 'POST',
  body: JSON.stringify({ usuario: 'maria', rol: 'editor' })
});
DOC;
