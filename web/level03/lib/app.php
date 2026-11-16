<?php
// Foro Quisqueya (nivel 3 del lab de webs completas).
require __DIR__ . '/lab.php';

const LAB_APP = 'Foro Quisqueya';
const LAB_HINT = 'el moderador lo lee todo';
const LAB_EXTRA = 'Servidor de exploits: http://127.0.0.1:8290';
const MOD_COOKIE = 'mod_sesion';

function temas(): array
{
    $t = datos_leer('temas');
    if (!$t) {
        $t = [
            1 => ['id' => 1, 'titulo' => '¿Dónde comer el mejor mofongo en la capital?', 'mensajes' => [
                ['autor' => 'yokasta', 'fecha' => '2026-10-28 18:02', 'texto' => 'Recomienden, que el sábado tengo visita de fuera.'],
                ['autor' => 'elpana', 'fecha' => '2026-10-28 19:15', 'texto' => 'Por la <b>Duarte</b> hay uno que es otra cosa.'],
            ]],
            2 => ['id' => 2, 'titulo' => 'Reglas del foro (leer antes de publicar)', 'mensajes' => [
                ['autor' => 'moderador', 'fecha' => '2026-09-01 08:00', 'texto' => 'Se permite HTML básico para dar formato: <b>negrita</b>, <i>cursiva</i>, enlaces e imágenes. Los scripts se eliminan automáticamente. Si ves algo raro, dale a <i>Reportar</i> y lo reviso enseguida.'],
            ]],
            3 => ['id' => 3, 'titulo' => 'Se va la luz otra vez en Los Mina', 'mensajes' => [
                ['autor' => 'ramon_ld', 'fecha' => '2026-11-10 21:47', 'texto' => 'Tres apagones hoy. ¿A alguien más le pasa?'],
            ]],
        ];
        datos_guardar('temas', $t);
    }
    return $t;
}

// HTML básico permitido; los scripts se eliminan.
function limpiar(string $html): string
{
    return preg_replace('#<\s*/?\s*script[^>]*>#i', '', $html);
}

function es_moderador(): bool
{
    $t = admin_token();
    return $t !== '' && hash_equals($t, (string) ($_COOKIE[MOD_COOKIE] ?? ''));
}

function apodo(): string
{
    return $_SESSION['apodo'] ?? '';
}

function cabecera(string $tagline): void
{
    $a = apodo();
    $sesion = es_moderador() ? 'moderador' : ($a !== '' ? e($a) : 'invitado');
    lab_header($tagline, ['/' => 'Temas', '/nuevo.php' => 'Nuevo tema', '/moderacion/' => 'Moderación'], $sesion);
}
