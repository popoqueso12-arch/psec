<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: index.php');
    exit;
}

$log_file = __DIR__ . '/../logs/spam_ips.log';

// Borrar log
if (isset($_GET['clear']) && $_GET['clear'] === 'si') {
    @file_put_contents($log_file, '');
    header('Location: spam-log.php');
    exit;
}

$lineas = [];
if (file_exists($log_file)) {
    $raw = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lineas = array_reverse($raw); // más recientes primero
}

// Contar IPs únicas
$ips = [];
foreach ($lineas as $l) {
    $partes = explode(' | ', $l);
    if (isset($partes[1])) $ips[trim($partes[1])] = ($ips[trim($partes[1])] ?? 0) + 1;
}
arsort($ips);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Log de Spam IPs</title>
    <style>
        body { font-family: monospace; background: #0f0f0f; color: #e0e0e0; margin: 0; padding: 20px; }
        h2 { color: #f1416c; margin-bottom: 4px; }
        .sub { color: #888; font-size: 13px; margin-bottom: 20px; }
        .stats { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
        .stat-card { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 14px 20px; min-width: 140px; }
        .stat-card .val { font-size: 28px; font-weight: 700; color: #f1416c; }
        .stat-card .lbl { font-size: 11px; color: #888; text-transform: uppercase; }
        .top-ips { background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; }
        .top-ips h3 { margin: 0 0 10px; color: #ffc700; font-size: 13px; }
        .ip-row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px solid #222; font-size: 13px; }
        .ip-row:last-child { border-bottom: none; }
        .ip-addr { color: #79b6ff; }
        .ip-link { font-size: 11px; color: #50cd89; text-decoration: none; margin-left: 10px; }
        .ip-link:hover { text-decoration: underline; }
        .ip-count { color: #f1416c; font-weight: 700; }
        .log-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .log-table th { background: #1e1e2e; color: #888; text-align: left; padding: 8px 10px; border-bottom: 1px solid #333; }
        .log-table td { padding: 6px 10px; border-bottom: 1px solid #1a1a1a; vertical-align: top; }
        .log-table tr:hover td { background: #1a1a1a; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .badge-rojo { background: #3d0a14; color: #f1416c; }
        .badge-naranja { background: #3d2200; color: #ffc700; }
        .badge-azul { background: #0a1e3d; color: #79b6ff; }
        .acciones { display: flex; gap: 12px; margin-bottom: 18px; }
        .btn { padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; border: none; text-decoration: none; display: inline-block; }
        .btn-rojo { background: #f1416c; color: #fff; }
        .btn-gris { background: #333; color: #ccc; }
        .empty { color: #555; text-align: center; padding: 40px; }
        .ua { color: #666; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body>
<h2>🛡️ Log de IPs Spam</h2>
<p class="sub">Registros de intentos bloqueados en inicio.php y rate-limit</p>

<div class="stats">
    <div class="stat-card">
        <div class="val"><?= count($lineas) ?></div>
        <div class="lbl">Total intentos</div>
    </div>
    <div class="stat-card">
        <div class="val"><?= count($ips) ?></div>
        <div class="lbl">IPs únicas</div>
    </div>
    <div class="stat-card">
        <div class="val"><?= $ips ? max($ips) : 0 ?></div>
        <div class="lbl">Máx por IP</div>
    </div>
</div>

<?php if ($ips): ?>
<div class="top-ips">
    <h3>🔥 IPs más activas</h3>
    <?php $top = array_slice($ips, 0, 10, true); foreach ($top as $ip => $cnt): ?>
    <div class="ip-row">
        <span>
            <span class="ip-addr"><?= htmlspecialchars($ip) ?></span>
            <a class="ip-link" href="https://ipinfo.io/<?= urlencode($ip) ?>" target="_blank">↗ ipinfo</a>
            <a class="ip-link" href="https://www.abuseipdb.com/check/<?= urlencode($ip) ?>" target="_blank">↗ AbuseIPDB</a>
        </span>
        <span class="ip-count"><?= $cnt ?> intentos</span>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="acciones">
    <a href="spam-log.php?clear=si" class="btn btn-rojo" onclick="return confirm('¿Borrar todo el log?')">🗑 Limpiar log</a>
    <a href="index.php" class="btn btn-gris">← Panel</a>
</div>

<?php if (empty($lineas)): ?>
    <div class="empty">Sin registros de spam aún.</div>
<?php else: ?>
<table class="log-table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>IP</th>
            <th>Motivo</th>
            <th>User-Agent</th>
            <th>URI</th>
            <th>Extra</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach (array_slice($lineas, 0, 200) as $linea):
        $p = array_pad(explode(' | ', $linea), 6, '');
        $motivo = trim($p[2] ?? '');
        $badgeClass = str_contains($motivo, 'bloqueo') ? 'badge-rojo' : (str_contains($motivo, 'activo') ? 'badge-naranja' : 'badge-azul');
    ?>
    <tr>
        <td style="white-space:nowrap;color:#888"><?= htmlspecialchars(trim($p[0])) ?></td>
        <td>
            <a href="https://ipinfo.io/<?= urlencode(trim($p[1])) ?>" target="_blank" style="color:#79b6ff;text-decoration:none">
                <?= htmlspecialchars(trim($p[1])) ?>
            </a>
        </td>
        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($motivo) ?></span></td>
        <td class="ua" title="<?= htmlspecialchars(trim($p[3])) ?>"><?= htmlspecialchars(trim($p[3])) ?></td>
        <td style="color:#666;font-size:11px"><?= htmlspecialchars(trim($p[4])) ?></td>
        <td style="color:#888;font-size:11px"><?= htmlspecialchars(trim($p[5])) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</body>
</html>
