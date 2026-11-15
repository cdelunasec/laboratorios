// Panel: cada rol ve sus opciones. La seguridad "está en el frontend".
(function () {
  const panel = document.getElementById('panel');

  function tabla(filas) {
    const t = document.createElement('table');
    t.className = 'data';
    for (const f of filas) {
      const tr = t.insertRow();
      tr.insertCell().textContent = f.usuario;
      tr.insertCell().textContent = f.correo;
    }
    panel.appendChild(t);
  }

  if (YO.rol === 'admin') {
    // Directorio completo del personal (solo administradores).
    fetch('/api/admin/usuarios.php').then(r => r.json()).then(tabla);
  } else {
    fetch('/api/perfil.php').then(r => r.json()).then(p => tabla([p]));
  }
})();
