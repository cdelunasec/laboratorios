<?php
require '/var/www/lib/app.php';

cabecera('');
?>
  <section class="hero ld">
    <div>
      <h1>Vuelta a clases <em>sin hacer fila.</em></h1>
      <p>Útiles escolares y de oficina con entrega en todo Santo Domingo. Pides hoy, te llega mañana.</p>
      <div class="acciones">
        <a class="btn" href="#catalogo">Ver catálogo</a>
<?php if (!yo()): ?>
        <a class="btn ghost" href="/registro.php">Crear cuenta</a>
<?php endif; ?>
      </div>
    </div>
    <div class="sello"><div><b>24 h</b><span>entrega en<br>la capital</span></div></div>
  </section>

  <section class="seccion" id="catalogo">
    <h2 class="ld">Catálogo</h2>
    <div class="estante">
<?php foreach (productos() as $p): ?>
      <article class="producto spot ld">
        <div class="ilus" style="--tinte: <?= e($p['tinte']) ?>; --tinta: <?= e($p['tinta']) ?>"><?= icono($p['ico']) ?></div>
        <h3><?= e($p['nombre']) ?></h3>
        <div class="fila">
          <span class="precio"><small>RD$</small><?= number_format($p['precio']) ?></span>
          <span class="chip gris"><?= e($p['cat']) ?></span>
        </div>
      </article>
<?php endforeach; ?>
    </div>
  </section>

  <section class="seccion ventajas">
    <div class="ventaja ld">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg>
      <div><b>Entrega al día siguiente</b><span>En el Gran Santo Domingo.</span></div>
    </div>
    <div class="ventaja ld">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18M7 15h3"/></svg>
      <div><b>Pago contra entrega</b><span>Efectivo o tarjeta al recibir.</span></div>
    </div>
    <div class="ventaja ld">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21a9 9 0 1 0-9-9"/><path d="M3 21v-5h5M12 7v5l3 2"/></svg>
      <div><b>¿Perdiste el acceso?</b><span><a href="/olvide.php">Recupera tu contraseña</a> en un minuto.</span></div>
    </div>
  </section>
<?php
lab_footer();
