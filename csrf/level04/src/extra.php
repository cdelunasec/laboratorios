<?php
// Cada usuario tiene su token anti-CSRF para sus propios formularios.
$q = quien();
if ($q !== null) {
    echo '    <h2>Tus formularios</h2>' . "\n";
    echo '    <p class="dim">Tu token anti-CSRF (lo llevan todos tus formularios): <code>' . e(csrf_token($q)) . "</code></p>\n";
}
