<?php
// Panel local de flags (LFI, http-headers, IDOR, SSRF, XSS y CSRF). Nunca conoce los flags: compara
// hash('sha256', flag) con el hash que dejó cada nivel al arrancar.

const FLAGS = '/flags';
const PROGRESO = '/flags/panel/progreso.json';
const MAX_HISTORIAL = 20;

$CATS = require __DIR__ . '/config.php';

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function hash_actual(string $cat, int $n): ?string
{
    $f = FLAGS . "/$cat/" . sprintf('%02d', $n);
    if (!is_file($f)) {
        return null;
    }
    $h = trim((string) file_get_contents($f));
    return preg_match('/^[0-9a-f]{64}$/', $h) ? $h : null;
}

function progreso_leer(): array
{
    $p = json_decode((string) @file_get_contents(PROGRESO), true);
    return is_array($p) ? $p + ['resueltos' => [], 'historial' => []] : ['resueltos' => [], 'historial' => []];
}

function progreso_guardar(array $p): void
{
    @file_put_contents(PROGRESO, json_encode($p, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

$p = progreso_leer();

// Guarda los hashes que va viendo, para reconocer flags de un nivel reiniciado.
$cambio = false;
foreach ($CATS as $cat => $c) {
    foreach (array_keys($c['niveles']) as $n) {
        $h = hash_actual($cat, $n);
        $k = sprintf('%02d', $n);
        if ($h !== null && !in_array($h, $p['historial'][$cat][$k] ?? [], true)) {
            $p['historial'][$cat][$k][] = $h;
            $p['historial'][$cat][$k] = array_slice($p['historial'][$cat][$k], -MAX_HISTORIAL);
            $cambio = true;
        }
    }
}

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'validar') {
        $flag = trim((string) ($_POST['flag'] ?? ''));
        if (!preg_match('/^cdelunasec\{([a-z][a-z-]*?)-(\d{2})_[0-9a-f]+\}$/', $flag, $m)) {
            $msg = ['err', 'Formato inválido. Un flag se ve así: cdelunasec{categoria-NN_hex}.'];
        } elseif (!isset($CATS[$m[1]]) || !isset($CATS[$m[1]]['niveles'][(int) $m[2]])) {
            $msg = ['err', "El panel no conoce la categoría o el nivel ({$m[1]} {$m[2]})."];
        } else {
            [$cat, $k, $n] = [$m[1], $m[2], (int) $m[2]];
            $alias = $CATS[$cat]['alias'];
            $actual = hash_actual($cat, $n);
            $h = hash('sha256', $flag);

            if ($actual === null) {
                $msg = ['err', "No hay hash para $cat nivel $k: ese nivel nunca se ha levantado. Usa ./lab.sh up $alias $n"];
            } elseif (hash_equals($actual, $h)) {
                if (!isset($p['resueltos'][$cat][$k])) {
                    $p['resueltos'][$cat][$k] = ['fecha' => date('c'), 'hash' => $h];
                    $cambio = true;
                }
                $msg = ['ok', "¡Correcto! $cat nivel $k resuelto."];
            } elseif (in_array($h, $p['historial'][$cat][$k] ?? [], true)) {
                $msg = ['err', "Ese flag era de $cat nivel $k, pero no coincide con el flag actual: si reiniciaste el nivel, el flag cambió."];
            } else {
                $msg = ['err', "Incorrecto para $cat nivel $k. (Si reiniciaste el nivel, el flag cambió.)"];
            }
        }
    } elseif ($accion === 'reiniciar' && isset($CATS[$_POST['cat'] ?? ''])) {
        unset($p['resueltos'][$_POST['cat']]);
        $cambio = true;
        $msg = ['ok', 'Progreso de ' . $CATS[$_POST['cat']]['nombre'] . ' reiniciado.'];
    }
}

if ($cambio) {
    progreso_guardar($p);
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Panel de flags · cdelunasec/labs</title>
<link rel="stylesheet" href="/assets/style.css">
<style>
  .wrap { max-width: 880px; }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 10px; margin: 0 0 18px; }
  .lvl { padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font-size: 13px; }
  .lvl b { font-family: var(--mono); }
  .lvl .st { float: right; font-size: 12px; }
  .lvl.ok { border-color: var(--accent); }
  .lvl.ok .st { color: var(--accent); }
  .lvl.off { opacity: .55; }
  .lvl .meta { color: var(--muted); font-size: 12px; }
  .bar { height: 8px; border-radius: 99px; background: var(--bg); border: 1px solid var(--line); overflow: hidden; margin: 6px 0 14px; }
  .bar span { display: block; height: 100%; background: var(--accent); }
  .cat-head { display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; }
  .cat-head h2 { margin-right: auto; }
  .cat-head form { margin: 0; }
  .cat-head button { padding: 4px 10px; font-size: 12px; background: transparent; color: var(--muted); border: 1px solid var(--line); }
</style>
</head>
<body>
<header class="lab-bar">
  <span class="brand">cdelunasec<span class="dim">/labs</span></span>
  <span class="tag level">panel de flags</span>
</header>
<main class="wrap">
  <section class="app">
    <h1>Panel de flags</h1>
    <p class="tagline">Pega un flag y el panel te dice si es el del nivel. Nunca conoce los flags: solo compara hashes.</p>
<?php if ($msg): ?>
    <div class="alert <?= e($msg[0]) ?>"><?= e($msg[1]) ?></div>
<?php endif; ?>
    <form class="stack" method="post">
      <input type="hidden" name="accion" value="validar">
      <input type="text" name="flag" placeholder="cdelunasec{...}" autocomplete="off" autofocus>
      <button>Validar</button>
    </form>

<?php foreach ($CATS as $cat => $c):
    $total = count($c['niveles']);
    $hechos = count($p['resueltos'][$cat] ?? []);
    $pct = $total ? (int) round(100 * $hechos / $total) : 0;
?>
    <div class="cat-head">
      <h2><?= e($c['nombre']) ?> <span class="dim">· <?= $hechos ?>/<?= $total ?></span></h2>
      <form method="post" onsubmit="return confirm('¿Reiniciar el progreso de <?= e($c['nombre']) ?>? No se puede deshacer.');">
        <input type="hidden" name="accion" value="reiniciar">
        <input type="hidden" name="cat" value="<?= e($cat) ?>">
        <button>Reiniciar progreso</button>
      </form>
    </div>
    <div class="bar"><span style="width: <?= $pct ?>%"></span></div>
    <div class="grid">
<?php foreach ($c['niveles'] as $n => [$pista, $tipo]):
        $k = sprintf('%02d', $n);
        $res = $p['resueltos'][$cat][$k] ?? null;
        $levantado = hash_actual($cat, $n) !== null;
        [$clase, $estado] = $res ? ['ok', 'resuelto'] : ($levantado ? ['', 'pendiente'] : ['off', 'sin levantar']);
?>
      <div class="lvl <?= $clase ?>" title="<?= $res ? 'Resuelto el ' . e($res['fecha']) : '' ?>">
        <span class="st"><?= $estado ?></span>
        <b>Nivel <?= $k ?></b>
        <div><?= e($pista) ?></div>
        <div class="meta"><?= e($tipo) ?> · :<?= e($c['puerto'] . $k) ?></div>
      </div>
<?php endforeach; ?>
    </div>
<?php endforeach; ?>
  </section>
  <p class="goal">El progreso se guarda en .flags/panel/progreso.json</p>
</main>
</body>
</html>
