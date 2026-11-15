<?php
require '/var/www/lab/lab.php';

lab_header('20', 'UniRed', 'La red social de tu universidad.', 'jefe final');
lab_nav(['?page=muro.php' => 'Muro', '?page=perfil.php' => 'Mi perfil', '?page=ayuda.php' => 'Ayuda']);

// Avatar: solo .gif, y verificamos que de verdad empiece como GIF.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $f = $_FILES['avatar'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $magic = $f['error'] === UPLOAD_ERR_OK ? file_get_contents($f['tmp_name'], false, null, 0, 4) : '';
    if ($ext !== 'gif') {
        lab_alert('err', 'El avatar tiene que ser .gif');
    } elseif ($magic !== 'GIF8') {
        lab_alert('err', 'Eso no es un GIF de verdad.');
    } else {
        $name = bin2hex(random_bytes(6)) . '.gif';
        move_uploaded_file($f['tmp_name'], 'uploads/avatars/' . $name);
        lab_alert('ok', 'Avatar actualizado:', '/uploads/avatars/' . $name);
    }
}

$page = $_GET['page'] ?? 'muro.php';

if (preg_match('#[a-z0-9]+://|data:#i', $page)) {
    lab_alert('err', 'Wrappers no permitidos.');
} else {
    $page = str_replace('../', '', $page);
    include 'pages/' . $page;
}

lab_footer('consigue ejecución de comandos, lista / y lee el archivo /flag_*.txt.');
