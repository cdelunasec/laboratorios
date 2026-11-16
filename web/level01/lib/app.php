<?php
// Papelería Colón: tienda en línea (nivel 1 del lab de webs completas).
require __DIR__ . '/lab.php';

const LAB_APP = 'Papelería Colón';
const LAB_HINT = 'las cuentas de los demás';
const LAB_EXTRA = 'puedes crearte una cuenta';
const LAB_TEMA = 'tienda';
const LAB_PIE = 'Papelería Colón · útiles escolares y de oficina · Santo Domingo';
const LAB_LOGO = '<svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><rect x="3" y="3" width="26" height="26" rx="8" fill="#ff6347"/><path d="M10 22.5 19.5 13l2.5 2.5L12.5 25H10z" fill="#1c0905"/><path d="m20.5 12 1.8-1.8a1.4 1.4 0 0 1 2 0l.5.5a1.4 1.4 0 0 1 0 2L23 14.5z" fill="#1c0905"/><path d="M9 9h8M9 12.5h5" stroke="#1c0905" stroke-width="2" stroke-linecap="round"/></svg>';

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
        ['nombre' => 'Cuaderno espiral 100 hojas', 'cat' => 'Escolar', 'precio' => 95, 'ico' => 'cuaderno', 'tinte' => '#3a1f1a', 'tinta' => '#ff9a85'],
        ['nombre' => 'Lápices de colores (24)', 'cat' => 'Arte', 'precio' => 240, 'ico' => 'colores', 'tinte' => '#3a2a14', 'tinta' => '#ffc35c'],
        ['nombre' => 'Mochila escolar', 'cat' => 'Escolar', 'precio' => 1450, 'ico' => 'mochila', 'tinte' => '#1f2a1f', 'tinta' => '#7bdc9a'],
        ['nombre' => 'Resma papel carta', 'cat' => 'Oficina', 'precio' => 395, 'ico' => 'resma', 'tinte' => '#2a2124', 'tinta' => '#f3e9e6'],
        ['nombre' => 'Calculadora científica', 'cat' => 'Oficina', 'precio' => 890, 'ico' => 'calculadora', 'tinte' => '#1f2333', 'tinta' => '#9fb4ff'],
        ['nombre' => 'Marcadores de pizarra (4)', 'cat' => 'Oficina', 'precio' => 180, 'ico' => 'marcadores', 'tinte' => '#331a22', 'tinta' => '#ff8fb0'],
    ];
}

// Ilustraciones de línea de los productos (usan currentColor).
function icono(string $k): string
{
    $s = 'fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"';
    $p = [
        'cuaderno' => '<rect x="16" y="8" width="34" height="48" rx="4"/><path d="M12 16h8M12 26h8M12 36h8M12 46h8M26 20h16M26 28h12"/>',
        'colores' => '<path d="M14 50 28 14l6 2-10 38zM30 50l10-36 6 1-4 37zM44 50l8-34 6 2-6 33z"/><path d="M12 54h44"/>',
        'mochila' => '<path d="M18 26a14 14 0 0 1 28 0v26a4 4 0 0 1-4 4H22a4 4 0 0 1-4-4z"/><path d="M26 12v-2a6 6 0 0 1 12 0v2M24 38h16v10H24z"/>',
        'resma' => '<path d="M14 22 32 12l18 10-18 10z"/><path d="M14 30l18 10 18-10M14 38l18 10 18-10"/>',
        'calculadora' => '<rect x="16" y="8" width="32" height="48" rx="5"/><path d="M22 16h20v8H22zM23 33h2M31 33h2M39 33h2M23 41h2M31 41h2M39 41h2M23 49h2M31 49h2M39 49h2"/>',
        'marcadores' => '<path d="m16 48 22-22 6 6-22 22h-6z"/><path d="m40 24 6-6a3 3 0 0 1 4 0l2 2a3 3 0 0 1 0 4l-6 6"/><path d="M12 56h16"/>',
    ];
    return '<svg viewBox="0 0 64 64" ' . $s . ' aria-hidden="true">' . ($p[$k] ?? '') . '</svg>';
}

// $titulo '' = la página pinta su propio encabezado.
function cabecera(string $titulo, string $sub = '', bool $estrecha = false): void
{
    $u = yo();
    $menu = ['/' => 'Tienda'];
    if ($u) {
        $menu['/cuenta.php'] = 'Mi cuenta';
        if ($u['rol'] === 'admin') {
            $menu['/admin/'] = 'Administración';
        }
        $sesion = '<span class="avatar">' . e(inicial($u['usuario'])) . '</span>' . e($u['usuario']) . ' <a href="/logout.php">Salir</a>';
    } else {
        $sesion = '<a href="/login.php">Entrar</a> <a class="btn" href="/registro.php">Crear cuenta</a>';
    }
    lab_header($titulo, $menu, $sesion, ['sub' => $sub, 'estrecha' => $estrecha]);
}
