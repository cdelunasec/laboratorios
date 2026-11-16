<?php
require '/var/www/lib/app.php';

$u = exigir_login();
cabecera('Tus datos y tus pedidos.');
?>
    <div class="card" id="perfil" data-id="<?= (int) $u['id'] ?>">
      <p class="muted">Cargando tu perfil...</p>
    </div>
    <h2>Pedidos</h2>
    <p class="muted">Todavía no has hecho ningún pedido.</p>
    <script src="/assets/cuenta.js"></script>
<?php
lab_footer();
