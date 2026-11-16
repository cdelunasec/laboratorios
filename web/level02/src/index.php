<?php
require '/var/www/lib/app.php';

$abiertos = 0;
foreach (tickets() as $t) {
    if ((datos_ticket($t)['Estado'] ?? '') === 'abierto') {
        $abiertos++;
    }
}

cabecera('');
?>
  <section class="hero">
    <div class="promesa ld">
      <h1>¿Algo se rompió? <span>Lo arreglamos.</span></h1>
      <p>Soporte técnico para pymes: redes, impresoras, correo y lo que se dañe. Abre un ticket y un técnico te responde en menos de 24 horas.</p>
      <div class="acciones">
        <a class="btn" href="/nuevo.php">Abrir un ticket</a>
        <a class="btn ghost" href="/ticket.php">Consultar mi ticket</a>
      </div>
    </div>
    <aside class="estado ld">
      <h2><span class="punto latido"></span>Estado de los servicios</h2>
      <ul>
        <li><span class="punto"></span><span class="nombre">Mesa de ayuda</span><span class="valor">en línea</span></li>
        <li><span class="punto"></span><span class="nombre">Soporte remoto</span><span class="valor">en línea</span></li>
        <li><span class="punto" style="background: var(--warn)"></span><span class="nombre">Visitas a domicilio</span><span class="valor">agenda llena hoy</span></li>
      </ul>
      <div class="resumen">
        <div><b>&lt; 24 h</b>primera respuesta</div>
        <div><b><?= $abiertos ?></b>tickets abiertos</div>
      </div>
    </aside>
  </section>

  <section class="seccion">
    <h2 class="ld">En qué te ayudamos</h2>
    <div class="areas">
      <div class="area spot ld">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5a10 10 0 0 1 14 0M8.5 16a5 5 0 0 1 7 0M2 9a15 15 0 0 1 20 0"/><circle cx="12" cy="19.5" r="1"/></svg>
        <h3>Redes y WiFi</h3><p>Routers, VLANs, cableado y ese WiFi que se cae por la tarde.</p>
      </div>
      <div class="area spot ld">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
        <h3>Correo</h3><p>Cuentas nuevas, Outlook, buzones llenos y reglas que nadie entiende.</p>
      </div>
      <div class="area spot ld">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 9V3h10v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M7 14h10v7H7z"/></svg>
        <h3>Impresoras</h3><p>Instalación, drivers, colas trabadas y atascos de papel.</p>
      </div>
    </div>
  </section>
<?php
lab_footer();
