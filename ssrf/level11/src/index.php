<?php
require '/var/www/lab/lab.php';

lab_header('11', 'Catálogo Mayorista', 'Importa tu catálogo en XML.', 'entidades externas');

$xml = (string) ($_POST['xml'] ?? '');
if ($xml === '') {
    $xml = "<?xml version=\"1.0\"?>\n<catalogo>\n  <producto><nombre>Arroz selecto 10 lb</nombre><precio>650</precio></producto>\n  <producto><nombre>Aceite de soya</nombre><precio>390</precio></producto>\n</catalogo>";
}
?>
    <p class="dim">El panel interno de administración está en el puerto 8080 de este servidor.</p>
    <form class="stack" method="post">
      <label>Catálogo (XML)</label>
      <textarea name="xml" rows="9"><?= e($xml) ?></textarea>
      <button>Importar</button>
    </form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc = new DOMDocument();
    // Resuelve entidades "para soportar catálogos con DTD del proveedor".
    if (!@$doc->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD)) {
        lab_alert('err', 'XML inválido.');
    } else {
        echo "    <table class=\"data\"><tr><th>Producto</th><th>Precio</th></tr>\n";
        foreach ($doc->getElementsByTagName('producto') as $p) {
            $n = $p->getElementsByTagName('nombre')->item(0)?->textContent ?? '';
            $pr = $p->getElementsByTagName('precio')->item(0)?->textContent ?? '';
            echo '      <tr><td><pre>' . e($n) . '</pre></td><td>' . e($pr) . "</td></tr>\n";
        }
        echo "    </table>\n";
    }
}

lab_footer('lee la clave del panel interno.');
