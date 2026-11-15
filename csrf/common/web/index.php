<?php
require '/var/www/lab/lab.php';

lab_header('Gestión de usuarios de la empresa.');

$q = quien();
if ($q === null) {
    echo '    <p>Entra con tu cuenta para ver tu rol. <a href="/login.php">Entrar</a></p>' . "\n";
} else {
    echo '    <p>Hola, <b>' . e($q) . '</b>. Tu rol es <code>' . e(rol_de($q)) . "</code>.</p>\n";
}
?>
    <h2>Cómo da roles el admin</h2>
    <p class="dim">El admin (con su sesión iniciada en <code>http://<?= e(lab_host()) ?></code>) usa este formulario:</p>
    <pre><?= e(LAB_DOC) ?></pre>
    <p class="dim">Su sesión es una cookie HttpOnly con <code>SameSite=<?= e(LAB_SAMESITE) ?></code>. Lee los enlaces que le mandas desde "Enviar enlace al admin".</p>
<?php
if (is_file('/var/www/html/extra.php')) {
    include '/var/www/html/extra.php';
}
lab_footer();
