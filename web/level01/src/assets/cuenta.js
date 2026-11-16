// Perfil del cliente: lo pide a la API de la tienda.
(function () {
  var caja = document.getElementById('perfil');
  var id = caja.getAttribute('data-id');
  fetch('/api/usuario.php?id=' + encodeURIComponent(id))
    .then(function (r) { return r.json(); })
    .then(function (u) {
      caja.innerHTML = '';
      [['Usuario', u.usuario], ['Correo', u.email], ['Cliente desde', u.creado]].forEach(function (f) {
        var p = document.createElement('p');
        p.innerHTML = '<span class="muted"></span> ';
        p.firstChild.textContent = f[0] + ':';
        p.appendChild(document.createTextNode(f[1]));
        caja.appendChild(p);
      });
    })
    .catch(function () { caja.textContent = 'No se pudo cargar el perfil.'; });
})();
