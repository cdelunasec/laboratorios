// Perfil del cliente: lo pide a la API de la tienda.
(function () {
  var caja = document.getElementById('perfil');
  var datos = caja.querySelector('.datos');
  var id = caja.getAttribute('data-id');
  fetch('/api/usuario.php?id=' + encodeURIComponent(id))
    .then(function (r) { return r.json(); })
    .then(function (u) {
      var dl = document.createElement('dl');
      [['Usuario', u.usuario], ['Correo', u.email], ['Cliente desde', u.creado]].forEach(function (f) {
        var dt = document.createElement('dt');
        var dd = document.createElement('dd');
        dt.textContent = f[0];
        dd.textContent = f[1];
        dl.appendChild(dt);
        dl.appendChild(dd);
      });
      datos.replaceChildren(dl);
    })
    .catch(function () {
      datos.textContent = 'No se pudo cargar tu perfil. Recarga la página para intentarlo otra vez.';
    });
})();
