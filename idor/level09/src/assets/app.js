// Cliente web de la clínica.
// Migrado a la API v2 (con control de acceso). La v1 se queda viva mientras
// actualizamos la app móvil vieja: /api/v1/expediente.php?id=...
const API = '/api/v2/expediente.php';

function cargarExpediente(id) {
  fetch(API + '?id=' + id)
    .then(r => r.json())
    .then(d => {
      document.getElementById('exp').textContent = d.error
        ? d.error
        : `Diagnóstico: ${d.diagnostico}\nNotas: ${d.notas}`;
    });
}
