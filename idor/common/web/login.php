<?php
require '/var/www/lab/lab.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = query('SELECT * FROM users WHERE username = ?', [$_POST['username'] ?? ''])->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['uid'] = $user['id'];
        header('Location: /');
        exit;
    }
    $error = 'Usuario o contraseña incorrectos.';
}

lab_header('Iniciar sesión. Cuenta de prueba: carlos / carlos123');

if ($error) {
    lab_alert('err', $error);
}
?>
    <form method="post" class="stack">
      <label for="username">Usuario</label>
      <input type="text" name="username" id="username" required>
      <label for="password">Contraseña</label>
      <input type="password" name="password" id="password" required>
      <button type="submit">Entrar</button>
    </form>
<?php
lab_footer();
