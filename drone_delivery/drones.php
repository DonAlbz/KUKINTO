<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';

if (!isset($_SESSION['operator_id'])) {
    header("Location: login_operatore.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM drones ORDER BY id ASC");
$drones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$disponibili = 0; $occupati = 0; $offline = 0;
foreach ($drones as $d) {
    if ($d['status'] === 'attivo')  $disponibili++;
    if ($d['status'] === 'occupato') $occupati++;
    if ($d['status'] === 'offline')  $offline++;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Gestione Droni</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
* { box-sizing: border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }
.page { max-width:1400px; margin:0 auto; padding:28px 28px 60px; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.page-title { color:#00eaff; font-size:22px; font-weight:600; margin:0; }
.btn-add { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:500; color:#00eaff; border:1px solid rgba(0,234,255,0.3); background:rgba(0,234,255,0.06); transition:0.15s; }
.btn-add:hover { filter:brightness(1.2); }

/* STAT CARDS */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.stat-card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:12px; padding:18px 20px; }
.stat-label { font-size:11px; color:#5a7a99; text-transform:uppercase; letter-spacing:0.7px; margin-bottom:6px; }
.stat-value { font-size:28px; font-weight:600; color:#00eaff; }
.stat-card.s-attivo  .stat-value { color:#00e676; }
.stat-card.s-occupato .stat-value { color:#ffb74d; }
.stat-card.s-offline  .stat-value { color:#ff5252; }

/* TABELLA */
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:separate; border-spacing:0 6px; min-width:700px; }
thead th { font-size:11px; font-weight:600; letter-spacing:0.8px; text-transform:uppercase; color:#5a7a99; padding:0 16px 8px; text-align:left; border-bottom:1px solid rgba(0,234,255,0.08); }
tbody tr { background:rgba(255,255,255,0.03); transition:background 0.15s; }
tbody tr:hover { background:rgba(0,234,255,0.05); }
tbody td { padding:14px 16px; color:#cfe8ff; font-size:13.5px; vertical-align:middle; }
tbody td:first-child { border-radius:10px 0 0 10px; }
tbody td:last-child  { border-radius:0 10px 10px 0; }

.drone-id { color:#5a7a99; font-size:12px; font-weight:600; font-family:monospace; }
.drone-name { font-weight:500; color:#e6f1ff; }

/* BADGE */
.badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
.badge::before { content:''; width:6px; height:6px; border-radius:50%; }
.b-attivo   { background:rgba(0,230,118,0.10);  color:#00e676; border:1px solid rgba(0,230,118,0.20); }
.b-attivo::before { background:#00e676; }
.b-occupato { background:rgba(255,183,77,0.12); color:#ffb74d; border:1px solid rgba(255,183,77,0.25); }
.b-occupato::before { background:#ffb74d; }
.b-offline  { background:rgba(255,82,82,0.10);  color:#ff5252; border:1px solid rgba(255,82,82,0.20); }
.b-offline::before { background:#ff5252; }

/* BATTERIA */
.battery { display:flex; align-items:center; gap:8px; }
.battery-bar { width:60px; height:6px; background:rgba(255,255,255,0.08); border-radius:3px; overflow:hidden; }
.battery-fill { height:100%; border-radius:3px; }
.battery-pct { font-size:12px; color:#8aaec8; }

/* COORDS */
.coords { font-size:12px; color:#5a7a99; font-family:monospace; }

/* AZIONI */
.actions { display:flex; align-items:center; gap:6px; }
.btn-action { display:inline-block; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; border:1px solid; transition:0.15s; }
.btn-edit  { color:#00eaff; border-color:rgba(0,234,255,0.25); background:rgba(0,234,255,0.06); }
.btn-track { color:#a78bfa; border-color:rgba(167,139,250,0.25); background:rgba(167,139,250,0.06); }
.btn-action:hover { filter:brightness(1.2); }
.no-order { font-size:12px; color:#5a7a99; }
</style>
</head>
<body>
<?php include "menu_operatore.php"; ?>

<div class="page">
    <div class="page-header">
        <h1 class="page-title">Gestione Droni</h1>
        <a href="add_drone.php" class="btn-add">+ Aggiungi drone</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Totale droni</div>
            <div class="stat-value"><?= count($drones) ?></div>
        </div>
        <div class="stat-card s-attivo">
            <div class="stat-label">Disponibili</div>
            <div class="stat-value"><?= $disponibili ?></div>
        </div>
        <div class="stat-card s-occupato">
            <div class="stat-label">In consegna</div>
            <div class="stat-value"><?= $occupati ?></div>
        </div>
        <div class="stat-card s-offline">
            <div class="stat-label">Offline</div>
            <div class="stat-value"><?= $offline ?></div>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Stato</th>
                    <th>Batteria</th>
                    <th>Posizione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($drones as $d):
                $statusClass = ['attivo'=>'b-attivo','occupato'=>'b-occupato','offline'=>'b-offline'][$d['status']] ?? 'b-offline';
                $statusLabel = ['attivo'=>'Attivo','occupato'=>'In consegna','offline'=>'Offline'][$d['status']] ?? $d['status'];
                $batt = (int)($d['battery'] ?? 0);
                $battColor = $batt >= 60 ? '#00e676' : ($batt >= 30 ? '#ffb74d' : '#ff5252');

                $stmtOrder = $pdo->prepare("SELECT id FROM orders WHERE assigned_drone = :d AND status IN ('in_attesa','in_consegna') LIMIT 1");
                $stmtOrder->execute([':d' => $d['id']]);
                $droneOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC);

                $lat = $d['latitude'] ?: ($d['location'] ? explode(',', $d['location'])[0] : '—');
                $lng = $d['longitude'] ?: ($d['location'] ? explode(',', $d['location'])[1] : '—');
            ?>
            <tr>
                <td><span class="drone-id">#<?= $d['id'] ?></span></td>
                <td><span class="drone-name"><?= htmlspecialchars($d['name']) ?></span></td>
                <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                <td>
                    <div class="battery">
                        <div class="battery-bar">
                            <div class="battery-fill" style="width:<?= $batt ?>%;background:<?= $battColor ?>"></div>
                        </div>
                        <span class="battery-pct"><?= $batt ?>%</span>
                    </div>
                </td>
                <td><span class="coords"><?= round((float)$lat, 5) ?>, <?= round((float)$lng, 5) ?></span></td>
                <td>
                    <div class="actions">
                        <a href="edit_drone.php?id=<?= $d['id'] ?>" class="btn-action btn-edit">Modifica</a>
                        <?php if ($droneOrder): ?>
                            <a href="order_details_operatore.php?id=<?= $droneOrder['id'] ?>" class="btn-action btn-track">Traccia</a>
                        <?php else: ?>
                            <span class="no-order">Nessun ordine</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>


