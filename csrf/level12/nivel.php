<?php
const LAB_APP = 'Gobierno Digital';
const LAB_HINT = 'jefe final';
const LAB_SAMESITE = 'Strict';
const LAB_DOC = <<<'DOC'
// API JSON. Exige Content-Type: application/json y el token en una cabecera.
// CORS solo para "nuestros subdominios".
const { token } = await (await fetch('/api/token.php', { credentials: 'include' })).json();
await fetch('/admin/rol.php', {
  method: 'POST',
  credentials: 'include',
  headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
  body: JSON.stringify({ usuario: 'maria', rol: 'editor' })
});
DOC;
