<?php
require '/var/www/lab/lab.php';

class Preferencias
{
    public $tema = 'oscuro';
    public $idioma = 'es';
}

// Lleva la cuenta de visitas en un archivo de la caché.
class RegistroVisitas
{
    public $archivo = '/var/www/html/cache/visitas.log';
    public $linea = '';

    public function __destruct()
    {
        if ($this->linea !== '') {
            file_put_contents($this->archivo, $this->linea . "\n", FILE_APPEND);
        }
    }
}

$prefs = null;
if (isset($_COOKIE['prefs'])) {
    $prefs = @unserialize(base64_decode($_COOKIE['prefs']));
}
if (!$prefs instanceof Preferencias) {
    $prefs = new Preferencias();
    setcookie('prefs', base64_encode(serialize($prefs)), ['path' => '/']);
}

$reg = new RegistroVisitas();
$reg->linea = date('c') . ' visita';

lab_header('19', 'Radio Merengue 96.1', 'La que más suena.', 'galleta serializada');
?>
    <!-- TODO: borrar index.php.bak antes de salir a producción -->
    <p>Escucha en vivo lo mejor del merengue y la bachata.</p>
    <p class="dim">Tema: <code><?= e((string) $prefs->tema) ?></code> · Idioma: <code><?= e((string) $prefs->idioma) ?></code></p>
<?php
lab_footer('ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt).');
