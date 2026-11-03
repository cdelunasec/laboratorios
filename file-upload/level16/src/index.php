<?php
require '/var/www/lab/lab.php';

lab_header('16', 'ThemeForge', 'Sube tu tema en un .zip y lo instalamos. La carpeta de subidas no ejecuta código.');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $zip = new ZipArchive();

    if ($file['error'] !== UPLOAD_ERR_OK) {
        lab_alert('err', 'Error al subir el archivo.');
    } elseif ($ext !== 'zip' || $zip->open($file['tmp_name']) !== true) {
        lab_alert('err', 'El tema tiene que ser un archivo .zip válido.');
    } else {
        $dir = 'uploads/' . bin2hex(random_bytes(4));
        mkdir($dir);
        $installed = [];

        // Extracción manual para poder listar los archivos instalados
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $target = $dir . '/' . $entry;

            if (str_ends_with($entry, '/')) {
                @mkdir($target, 0755, true);
                continue;
            }
            @mkdir(dirname($target), 0755, true);
            file_put_contents($target, $zip->getFromIndex($i));
            $installed[] = $entry;
        }
        $zip->close();

        lab_alert('ok', 'Tema instalado en /' . $dir . ' con ' . count($installed) . ' archivo(s).');
        echo "<ul>\n";
        foreach ($installed as $f) {
            echo '<li><code>' . e($f) . "</code></li>\n";
        }
        echo "</ul>\n";
    }
}

lab_upload_form('Tema (.zip)');
lab_footer();
