<?php
require_once 'config.php';
require_once 'auth_operatore.php';

if (!isset($_SESSION['operator_id'])) {
    header("Location: login_operatore.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID ordine mancante.");
}

$orderId = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) { die("Ordine non trovato."); }

function haversine($lat1, $lon1, $lat2, $lon2) {
    $R = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
    return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
}

function assignDrone($pdo, $orderId, $droneId) {
    // Aggiorna ordine — updated_at serve alla simulazione per sapere quando è partita la consegna
    $pdo->prepare("
        UPDATE orders
        SET assigned_drone = :d, status = 'in_consegna', updated_at = NOW()
        WHERE id = :id
    ")->execute([':d' => $droneId, ':id' => $orderId]);

    // Aggiorna drone
    $pdo->prepare("
        UPDATE drones
        SET status = 'occupato', current_order = :order_id
        WHERE id = :id
    ")->execute([':order_id' => $orderId, ':id' => $droneId]);

    // Svuota i vecchi punti di tracking per questo drone
    $pdo->prepare("DELETE FROM drone_tracking WHERE drone_id = :d")
        ->execute([':d' => $droneId]);
}

// ASSEGNAZIONE AUTOMATICA
if (isset($_GET['auto'])) {
    $stmt = $pdo->query("SELECT * FROM drones WHERE status = 'attivo' AND battery >= 20");
    $drones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $best = null;
    $bestDist = PHP_INT_MAX;
    foreach ($drones as $d) {
        $dist = haversine($d['latitude'], $d['longitude'], $order['pickup_lat'], $order['pickup_lng']);
        if ($dist < $bestDist) { $bestDist = $dist; $best = $d; }
    }

    if (!$best) { die("Nessun drone disponibile."); }

    assignDrone($pdo, $orderId, $best['id']);
    header("Location: order_details_operatore.php?id=$orderId");
    exit;
}

// ASSEGNAZIONE MANUALE
$drones = $pdo->query("SELECT * FROM drones WHERE status = 'attivo' ORDER BY battery DESC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $droneId = intval($_POST['drone_id']);
    assignDrone($pdo, $orderId, $droneId);
    header("Location: order_details_operatore.php?id=$orderId");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Assegna Drone — Ordine #<?= $orderId ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
* { box-sizing:border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }
.page { max-width:560px; margin:40px auto; padding:0 24px 60px; }
.page-header { display:flex; align-items:center; gap:14px; margin-bottom:28px; }
.back-btn { display:inline-flex; align-items:center; gap:6px; color:#5a7a99; font-size:13px; padding:6px 12px; border:1px solid rgba(90,122,153,0.3); border-radius:8px; transition:0.15s; }
.back-btn:hover { color:#00eaff; border-color:rgba(0,234,255,0.3); }
.page-title { color:#00eaff; font-size:20px; font-weight:600; margin:0; }
.card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:14px; padding:24px; margin-bottom:16px; }
.card-title { color:#00eaff; font-size:13px; font-weight:600; letter-spacing:0.7px; text-transform:uppercase; margin:0 0 16px; padding-bottom:10px; border-bottom:1px solid rgba(0,234,255,0.08); }
.info-row { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:13.5px; }
.info-row:last-child { border-bottom:none; }
.info-label { color:#5a7a99; }
.info-value { color:#cfe8ff; text-align:right; }
.field { margin-bottom:16px; }
.field label { display:block; font-size:12px; color:#5a7a99; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase; }
select {
    width:100%; padding:11px 14px;
    background:#0d1628;
    border:1px solid rgba(0,234,255,0.2);
    border-radius:10px;
    color:#e6f1ff;
    font-size:14px;
    outline:none;
    transition:0.2s;
}
select:focus { border-color:rgba(0,234,255,0.5); }
select option { background:#0d1628; color:#e6f1ff; }
.btn-assign {
    width:100%; padding:12px;
    background:rgba(0,234,255,0.08);
    border:1px solid rgba(0,234,255,0.35);
    border-radius:10px;
    color:#00eaff;
    font-size:14px; font-weight:600;
    cursor:pointer; transition:0.2s;
    margin-top:4px;
}
.btn-assign:hover { background:rgba(0,234,255,0.16); box-shadow:0 0 20px rgba(0,234,255,0.15); }
.btn-auto {
    display:block; width:100%; padding:12px;
    background:rgba(167,139,250,0.08);
    border:1px solid rgba(167,139,250,0.35);
    border-radius:10px;
    color:#a78bfa;
    font-size:14px; font-weight:600;
    text-align:center; transition:0.2s;
    margin-top:10px;
}
.btn-auto:hover { background:rgba(167,139,250,0.16); }
.no-drones { text-align:center; color:#5a7a99; padding:20px 0; font-size:14px; }
</style>
</head>
<body>
<?php include "menu_operatore.php"; ?>

<div class="page">
    <div class="page-header">
        <a href="orders_operatore.php" class="back-btn">&#8592; Ordini</a>
        <h1 class="page-title">Assegna Drone — #<?= $orderId ?></h1>
    </div>

    <div class="card">
        <p class="card-title">Dettagli ordine</p>
        <div class="info-row"><span class="info-label">Ritiro</span><span class="info-value"><?= htmlspecialchars($order['pickup_address'] ?? '—') ?></span></div>
        <div class="info-row"><span class="info-label">Consegna</span><span class="info-value"><?= htmlspecialchars($order['delivery_address'] ?? '—') ?></span></div>
        <div class="info-row"><span class="info-label">Priorità</span><span class="info-value"><?= ucfirst($order['priority'] ?? '—') ?></span></div>
    </div>

    <div class="card">
        <p class="card-title">Seleziona drone</p>
        <?php if (empty($drones)): ?>
            <div class="no-drones">Nessun drone attivo disponibile al momento.</div>
        <?php else: ?>
        <form method="POST">
            <div class="field">
                <label>Drone disponibile</label>
                <select name="drone_id" required>
                    <?php foreach ($drones as $d): ?>
                        <option value="<?= $d['id'] ?>">
                            Drone #<?= $d['id'] ?> — <?= htmlspecialchars($d['name']) ?> — Batteria: <?= $d['battery'] ?>%
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-assign">Assegna drone</button>
        </form>
        <a href="assign_drone.php?id=<?= $orderId ?>&auto=1" class="btn-auto">
            🚁 Assegna automaticamente il più vicino
        </a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
