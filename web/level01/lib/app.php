<?php
// Papelería Colón: tienda en línea (nivel 1 del lab de webs completas).
require __DIR__ . '/lab.php';

const LAB_APP = 'Papelería Colón';
const LAB_HINT = 'las cuentas de los demás';
const LAB_EXTRA = 'Puedes crearte una cuenta';

function usuarios(): array
{
    $u = datos_leer('usuarios');
    if (!$u) {
        $u = [
            1 => ['id' => 1, 'usuario' => 'admin', 'email' => 'admin@papeleriacolon.do', 'rol' => 'admin',
                  'clave' => password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT), 'creado' => '2024-02-11', 'reset' => null],
            2 => ['id' => 2, 'usuario' => 'maria', 'email' => 'maria.p@gmail.com', 'rol' => 'cliente',
                  'clave' => password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT), 'creado' => '2024-05-03', 'reset' => null],
            3 => ['id' => 3, 'usuario' => 'jose', 'email' => 'jose.r@hotmail.com', 'rol' => 'cliente',
                  'clave' => password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT), 'creado' => '2024-08-19', 'reset' => null],
        ];
        datos_guardar('usuarios', $u);
    }
    return $u;
}

function guardar_usuarios(array $u): void
{
    datos_guardar('usuarios', $u);
}

function buscar_usuario(string $nombre): ?array
{
    foreach (usuarios() as $u) {
        if ($u['usuario'] === $nombre) {
            return $u;
        }
    }
    return null;
}

function yo(): ?array
{
    $id = $_SESSION['uid'] ?? null;
    return $id === null ? null : (usuarios()[$id] ?? null);
}

function exigir_login(): array
{
    $u = yo();
    if (!$u) {
        lab_redirect('/login.php');
    }
    return $u;
}

function productos(): array
{
    return [
        ['nombre' => 'Cuaderno espiral 100 hojas', 'precio' => 95, 'img' => 'cuaderno.png'],
        ['nombre' => 'Lápices de colores (24)', 'precio' => 240, 'img' => 'colores.png'],
        ['nombre' => 'Mochila escolar', 'precio' => 1450, 'img' => 'mochila.png'],
        ['nombre' => 'Resma papel carta', 'precio' => 395, 'img' => 'resma.png'],
        ['nombre' => 'Calculadora científica', 'precio' => 890, 'img' => 'calculadora.png'],
        ['nombre' => 'Marcadores de pizarra (4)', 'precio' => 180, 'img' => 'marcadores.png'],
    ];
}

function cabecera(string $tagline): void
{
    $u = yo();
    $menu = ['/' => 'Tienda'];
    if ($u) {
        $menu['/cuenta.php'] = 'Mi cuenta';
        if ($u['rol'] === 'admin') {
            $menu['/admin/'] = 'Administración';
        }
        $sesion = e($u['usuario']) . ' <a href="/logout.php">Salir</a>';
    } else {
        $menu['/login.php'] = 'Entrar';
        $menu['/registro.php'] = 'Crear cuenta';
        $sesion = '<a href="/login.php">Entrar</a>';
    }
    lab_header($tagline, $menu, $sesion);
}
