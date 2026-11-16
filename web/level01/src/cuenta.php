<?php
require '/var/www/lib/app.php';

$u = exigir_login();
cabecera('Mi cuenta', 'Tus datos y tus pedidos.');
?>
  <div class="card perfil ld" id="perfil" data-id="<?= (int) $u['id'] ?>">
    <span class="avatar"><?= e(inicial($u['usuario'])) ?></span>
    <div class="datos">
      <div class="esqueleto" style="width: 40%; margin-bottom: 10px"></div>
      <div class="esqueleto" style="width: 65%; margin-bottom: 10px"></div>
      <div class="esqueleto" style="width: 30%"></div>
    </div>
  </div>

  <section class="seccion">
    <h2 class="ld">Pedidos</h2>
    <div class="card plano vacio ld">
      <strong>Todavía no has hecho ningún pedido.</strong>
      Aquí vas a ver lo que compres y cuándo te llega.
      <p style="margin: 16px 0 0"><a class="btn" href="/#catalogo">Ir al catálogo</a></p>
    </div>
  </section>
  <script src="/assets/cuenta.js"></script>
<?php
lab_footer();
