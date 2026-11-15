<?php
require '/var/www/lab/lab.php';

// Router del framework. Si hay un proxy delante que reescribe la URL, la ruta
// original llega en una cabecera (compatibilidad con IIS / proxies viejos).
$uri = $_SERVER['HTTP_X_ORIGINAL_URL'] ?? $_SERVER['HTTP_X_REWRITE_URL'] ?? $_SERVER['REQUEST_URI'];
$ruta = (string) parse_url($uri, PHP_URL_PATH);

switch ($ruta) {
    case '/':
    case '/index.php':
        lab_header('14', 'Repuestos Don Chelo', 'Piezas para carros y motores.', 'la URL original');
        lab_nav(['/' => 'Inicio', '/catalogo' => 'Catálogo']);
        echo "    <p>Repuestos originales y genéricos. Envíos a todo el país.</p>\n";
        break;

    case '/catalogo':
        lab_header('14', 'Repuestos Don Chelo', 'Catálogo.', 'la URL original');
        lab_nav(['/' => 'Inicio', '/catalogo' => 'Catálogo']);
        echo "    <table class=\"data\"><tr><th>Pieza</th><th>Precio</th></tr><tr><td>Filtro de aceite</td><td>RD$ 450</td></tr><tr><td>Pastillas de freno</td><td>RD$ 1,800</td></tr></table>\n";
        break;

    case '/admin':
        lab_header('14', 'Repuestos Don Chelo · Admin', 'Panel del dueño.', 'la URL original');
        lab_alert('ok', 'Panel de administración.');
        lab_show_flag('Clave del panel');
        break;

    default:
        http_response_code(404);
        lab_header('14', 'Repuestos Don Chelo', 'No encontrado.', 'la URL original');
        lab_alert('err', 'Esa página no existe.');
}

lab_footer('entra al panel /admin.');
