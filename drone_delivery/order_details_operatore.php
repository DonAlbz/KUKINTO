<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "auth_operatore.php";
require_once "config.php";

if (!isset($_SESSION['operator_id'])) {
    header("Location: login_operatore.php");
    exit;
}

if (!isset($_GET['id'])) { die("ID ordine mancante."); }
$orderId = intval($_GET['id']);

$stmt = $pdo->prepare("
    SELECT o.*, c.username AS cliente_username, c.email AS cliente_email
    FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id
    WHERE o.id = :id LIMIT 1
");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) { die("Ordine non trovato."); }

$statusMap = [
    'in_attesa'   => ['label' => 'In attesa',   'class' => 'b-attesa'],
    'in_consegna' => ['label' => 'In consegna', 'class' => 'b-consegna'],
    'completato'  => ['label' => 'Completato',  'class' => 'b-completato'],
    'annullato'   => ['label' => 'Annullato',   'class' => 'b-annullato'],
];
$statusInfo = $statusMap[$order['status']] ?? ['label' => ucfirst($order['status']), 'class' => 'b-attesa'];
$dateFmt = $order['created_at'] ? date('d M Y, H:i', strtotime($order['created_at'])) : '—';
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Ordine #<?= $orderId ?> — Operatore</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
* { box-sizing: border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }
.page { padding:28px 28px 60px; }
.page-header { display:flex; align-items:center; gap:14px; margin-bottom:24px; }
.back-btn { display:inline-flex; align-items:center; gap:6px; color:#5a7a99; font-size:13px; padding:6px 12px; border:1px solid rgba(90,122,153,0.3); border-radius:8px; transition:0.15s; }
.back-btn:hover { color:#00eaff; border-color:rgba(0,234,255,0.3); }
.page-title { color:#00eaff; font-size:20px; font-weight:600; margin:0; }
.grid { display:grid; grid-template-columns:380px 1fr; gap:20px; align-items:start; }
.card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:14px; padding:22px; margin-bottom:16px; }
.card:last-child { margin-bottom:0; }
.card-title { color:#00eaff; font-size:13px; font-weight:600; letter-spacing:0.7px; text-transform:uppercase; margin:0 0 16px; padding-bottom:10px; border-bottom:1px solid rgba(0,234,255,0.1); }
.info-row { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:13.5px; }
.info-row:last-child { border-bottom:none; }
.info-label { color:#5a7a99; white-space:nowrap; }
.info-value { color:#cfe8ff; text-align:right; }
.badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
.badge::before { content:''; width:6px; height:6px; border-radius:50%; }
.b-attesa    { background:rgba(144,202,249,0.12); color:#90caf9; border:1px solid rgba(144,202,249,0.25); }
.b-attesa::before { background:#90caf9; }
.b-consegna  { background:rgba(255,183,77,0.12);  color:#ffb74d; border:1px solid rgba(255,183,77,0.25); }
.b-consegna::before { background:#ffb74d; }
.b-completato{ background:rgba(0,230,118,0.10);   color:#00e676; border:1px solid rgba(0,230,118,0.20); }
.b-completato::before { background:#00e676; }
.b-annullato { background:rgba(255,82,82,0.10);   color:#ff5252; border:1px solid rgba(255,82,82,0.20); }
.b-annullato::before { background:#ff5252; }
.timeline { display:flex; flex-direction:column; }
.tl-step { display:flex; align-items:flex-start; gap:14px; opacity:0.35; transition:0.3s; }
.tl-step.active { opacity:1; }
.tl-dot-wrap { display:flex; flex-direction:column; align-items:center; }
.tl-dot { width:12px; height:12px; border-radius:50%; border:2px solid #5a7a99; background:transparent; flex-shrink:0; margin-top:2px; }
.tl-step.active .tl-dot { border-color:#00eaff; background:#00eaff; }
.tl-line { width:2px; flex:1; background:rgba(0,234,255,0.15); min-height:20px; }
.tl-step:last-child .tl-line { display:none; }
.tl-text { font-size:13.5px; color:#cfe8ff; padding-bottom:16px; }
.tl-step.active .tl-text { color:#e6f1ff; font-weight:500; }
.stat-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.stat-box { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.08); border-radius:10px; padding:12px 14px; }
.stat-label { font-size:11px; color:#5a7a99; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px; }
.stat-value { font-size:18px; font-weight:600; color:#00eaff; }
.actions { display:flex; flex-wrap:wrap; gap:8px; }
.btn-action { display:inline-block; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:500; border:1px solid; transition:0.15s; }
.btn-cyan   { color:#00eaff; border-color:rgba(0,234,255,0.3); background:rgba(0,234,255,0.06); }
.btn-orange { color:#ffb74d; border-color:rgba(255,183,77,0.3); background:rgba(255,183,77,0.06); }
.btn-red    { color:#ff5252; border-color:rgba(255,82,82,0.3); background:rgba(255,82,82,0.06); }
.btn-action:hover { filter:brightness(1.2); }
#map { height:420px; width:100%; border-radius:12px; border:1px solid rgba(0,234,255,0.1); }
</style>
</head>
<body>
<?php include "menu_operatore.php"; ?>

<div class="page">
    <div class="page-header">
        <a href="orders_operatore.php" class="back-btn">&#8592; Ordini</a>
        <h1 class="page-title">Ordine #<?= $orderId ?></h1>
        <span class="badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
    </div>

    <div class="grid">
        <div>
            <div class="card">
                <p class="card-title">Dettagli ordine</p>
                <div class="info-row"><span class="info-label">Cliente</span><span class="info-value"><?= htmlspecialchars($order['cliente_username'] ?? 'Anonimo') ?></span></div>
                <div class="info-row"><span class="info-label">Email</span><span class="info-value"><?= htmlspecialchars($order['cliente_email'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Ritiro</span><span class="info-value"><?= htmlspecialchars($order['pickup_address'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Consegna</span><span class="info-value"><?= htmlspecialchars($order['delivery_address'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Priorità</span><span class="info-value"><?= ucfirst($order['priority'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Creato il</span><span class="info-value"><?= $dateFmt ?></span></div>
                <div class="info-row"><span class="info-label">Drone assegnato</span><span class="info-value"><?= $order['assigned_drone'] ? '#'.$order['assigned_drone'] : 'Nessuno' ?></span></div>
            </div>

            <div class="card">
                <p class="card-title">Timeline</p>
                <div class="timeline">
                    <div class="tl-step" id="step1"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">Ordine creato</div></div>
                    <div class="tl-step" id="step2"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">Drone assegnato</div></div>
                    <div class="tl-step" id="step3"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">In consegna</div></div>
                    <div class="tl-step" id="step4"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">Consegnato</div></div>
                </div>
                <canvas id="miniChart" style="margin-top:16px;height:70px;"></canvas>
            </div>

            <div class="card">
                <p class="card-title">Azioni</p>
                <div class="actions">
                    <?php if ($order['status'] === 'in_attesa'): ?>
                        <a href="assign_drone.php?order_id=<?= $orderId ?>" class="btn-action btn-cyan">Assegna drone</a>
                    <?php endif; ?>
                    <?php if ($order['status'] === 'in_consegna'): ?>
                        <a href="order_force_complete.php?id=<?= $orderId ?>" class="btn-action btn-orange">Forza completamento</a>
                    <?php endif; ?>
                    <?php if (!in_array($order['status'], ['completato', 'annullato'])): ?>
                        <a href="order_cancel.php?id=<?= $orderId ?>" class="btn-action btn-red">Annulla ordine</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom:16px;">
                <p class="card-title">Drone in tempo reale</p>
                <div class="stat-grid">
                    <div class="stat-box"><div class="stat-label">Velocità</div><div class="stat-value" id="drone-speed">—</div></div>
                    <div class="stat-box"><div class="stat-label">Batteria</div><div class="stat-value" id="drone-battery">—</div></div>
                    <div class="stat-box"><div class="stat-label">Distanza rimasta</div><div class="stat-value" id="distance-remaining">—</div></div>
                    <div class="stat-box"><div class="stat-label">ETA</div><div class="stat-value" id="eta">—</div></div>
                </div>
            </div>

            <div class="card" style="padding:16px;">
                <p class="card-title">Mappa</p>
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

<script>
const status = "<?= $order['status'] ?>";
if (status === 'in_attesa') {
    document.getElementById('step1').classList.add('active');
}
if (status === 'in_consegna') {
    ['step1','step2','step3'].forEach(id => document.getElementById(id).classList.add('active'));
}
if (status === 'completato' || status === 'annullato') {
    ['step1','step2','step3','step4'].forEach(id => document.getElementById(id).classList.add('active'));
}

const map = L.map('map').setView([45.5416, 10.2118], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19 }).addTo(map);

// ICONE CUSTOM
function makeIcon(emoji, color) {
    return L.divIcon({
        className: '',
        html: `<div style="
            width:36px; height:36px;
            background:${color};
            border:3px solid #fff;
            border-radius:50% 50% 50% 0;
            transform:rotate(-45deg);
            box-shadow:0 0 10px ${color}88;
            display:flex; align-items:center; justify-content:center;
        "><span style="transform:rotate(45deg);font-size:16px;line-height:36px;margin-left:0px;">${emoji}</span></div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -36]
    });
}

function makeDroneIcon(battery) {
    const color = battery > 50 ? '#00e676' : battery > 20 ? '#ffb74d' : '#ff5252';
    return L.divIcon({
        className: '',
        html: `<div style="
            width:42px; height:42px;
            background:#0a0f1f;
            border:3px solid ${color};
            border-radius:50%;
            box-shadow:0 0 14px ${color};
            display:flex; align-items:center; justify-content:center;
            font-size:20px;
        ">🚁</div>`,
        iconSize: [42, 42],
        iconAnchor: [21, 21],
        popupAnchor: [0, -24]
    });
}

let droneMarker = null;
let pickupMarker = null;
let deliveryMarker = null;
let pathLine = null;
let lastPos = null, nextPos = null, animStart = null;

// Marker ritiro fisso
<?php if ($order['pickup_lat'] && $order['pickup_lng']): ?>
pickupMarker = L.marker(
    [<?= $order['pickup_lat'] ?>, <?= $order['pickup_lng'] ?>],
    { icon: makeIcon('📦', '#00eaff') }
).addTo(map).bindPopup('<b>📦 Punto di ritiro</b><br><?= htmlspecialchars(addslashes($order['pickup_address'] ?? '')) ?>');
<?php endif; ?>

function animateDrone(ts) {
    if (!lastPos || !nextPos) return;
    if (!animStart) animStart = ts;
    let p = Math.min((ts - animStart) / 2000, 1);
    droneMarker.setLatLng([
        lastPos.lat + (nextPos.lat - lastPos.lat) * p,
        lastPos.lng + (nextPos.lng - lastPos.lng) * p
    ]);
    if (p < 1) requestAnimationFrame(animateDrone);
}

function aggiornaTracking(){
    
    fetch('get_drone_position.php?order_id=<?= $orderId ?>')
        .then(r => r.json())
        .then(data => {
            if (data.error || !data.lat || !data.lng) return;

            const battery = data.battery ?? 100;

            if (!droneMarker) {
                droneMarker = L.marker([data.lat, data.lng], { icon: makeDroneIcon(battery) })
                    .addTo(map)
                    .bindPopup(`<b>🚁 Drone</b><br>Batteria: ${battery}%<br>Velocità: ${data.speed_kmh ?? '—'} km/h`);
                map.setView([data.lat, data.lng], 14);
            } else {
                lastPos = droneMarker.getLatLng();
                nextPos = { lat: data.lat, lng: data.lng };
                animStart = null;
                requestAnimationFrame(animateDrone);
                droneMarker.setIcon(makeDroneIcon(battery));
                droneMarker.setPopupContent(`<b>🚁 Drone</b><br>Batteria: ${battery}%<br>Velocità: ${data.speed_kmh ?? '—'} km/h`);
            }

            if (data.delivery?.lat && data.delivery?.lng && !deliveryMarker) {
                deliveryMarker = L.marker(
                    [data.delivery.lat, data.delivery.lng],
                    { icon: makeIcon('🏠', '#00e676') }
                ).addTo(map).bindPopup('<b>🏠 Punto di consegna</b><br><?= htmlspecialchars(addslashes($order['delivery_address'] ?? '')) ?>');
            }

            if (pathLine) map.removeLayer(pathLine);
            if (data.path?.length >= 2) {
                pathLine = L.polyline(
                    data.path.map(p => [p.lat, p.lng]),
                    { color:'#00eaff', weight:3, opacity:0.7, dashArray:'6,6' }
                ).addTo(map);
            }

            document.getElementById('drone-speed').textContent = data.speed_kmh !== null ? data.speed_kmh + ' km/h' : '—';
            document.getElementById('drone-battery').textContent = battery + '%';
            document.getElementById('distance-remaining').textContent = data.distance_remaining_km + ' km';
            document.getElementById('eta').textContent = data.eta_minutes !== null ? data.eta_minutes + ' min' : '—';
        })
        .catch(() => {});
}

setInterval(aggiornaTracking, 2000);
aggiornaTracking();
</script>
</body>
</html>


