<?php
require '/var/www/lab/lab.php';
require '/var/www/lab/cuentas.php';

lab_header('13', 'Correo Antillano', 'Recuperar contraseña.', 'el enlace lo arma tu petición');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = (string) ($_POST['usuario'] ?? '');
    $d = datos_leer();
    if (!isset($d['usuarios'][$u])) {
        lab_alert('err', 'Ese usuario no existe.');
    } else {
        $token = bin2hex(random_bytes(16));
        $d['tokens'][$token] = ['usuario' => $u, 'vence' => time() + 600];
        datos_guardar($d);

        // El enlace se arma con el Host de la petición "para que funcione en
        // cualquier dominio donde instalen el webmail".
        $enlace = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/reset.php?token=' . $token;

        if ($u === 'demo') {
            lab_alert('ok', 'Correo enviado a demo@correoantillano.local. Como es la cuenta de demostración, aquí tienes una copia:');
            echo '    <pre>Hola demo:' . "\n\n" . 'Para cambiar tu contraseña entra a:' . "\n" . e($enlace) . "\n\n" . 'El enlace vence en 10 minutos.</pre>' . "\n";
        } else {
            lab_alert('ok', "Correo enviado a $u@correoantillano.local.");
            if ($u === 'admin') {
                // El cliente de correo del admin abre (precarga) los enlaces de
                // los mensajes nuevos apenas llegan.
                @file_get_contents($enlace, false, stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]));
                echo '    <p class="dim">El admin siempre tiene el correo abierto; su cliente ya visitó el enlace.</p>' . "\n";
            }
        }
    }
}
?>
    <form class="stack" method="post">
      <label>Usuario</label>
      <input type="text" name="usuario">
      <button>Enviar enlace de recuperación</button>
    </form>
    <p><a href="/">Volver</a></p>
<?php
lab_footer('entra a la bandeja del usuario admin.');
