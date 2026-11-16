// Detalles de movimiento de las webs del lab: poquitos y a propósito.
(function () {
  // Entrada escalonada: numera los bloques .ld en orden de aparición.
  document.querySelectorAll('.ld').forEach(function (el, i) {
    el.style.setProperty('--i', Math.min(i, 10));
  });

  // Luz que sigue al cursor en las tarjetas .spot.
  document.addEventListener('pointermove', function (ev) {
    var c = ev.target.closest && ev.target.closest('.spot');
    if (!c) return;
    var r = c.getBoundingClientRect();
    c.style.setProperty('--mx', (ev.clientX - r.left) + 'px');
    c.style.setProperty('--my', (ev.clientY - r.top) + 'px');
  });
})();
