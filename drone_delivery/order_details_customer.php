<?php
require_once "auth_customer.php";
require_once "config.php";

$customer_id = $_SESSION['customer_id'];
$order_id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id AND customer_id = :cid LIMIT 1");
$stmt->execute([':id' => $order_id, ':cid' => $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) { die("Accesso non autorizzato."); }

$statusMap = [
    'in_attesa'   => ['label' => 'In attesa',   'class' => 'b-attesa'],
    'in_consegna' => ['label' => 'In consegna', 'class' => 'b-consegna'],
    'completato'  => ['label' => 'Completato',  'class' => 'b-completato'],
    'annullato'   => ['label' => 'Annullato',   'class' => 'b-annullato'],
];
$si = $statusMap[$order['status']] ?? ['label' => ucfirst($order['status']), 'class' => 'b-attesa'];
$dateFmt = date('d M Y, H:i', strtotime($order['created_at']));
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Ordine #<?= $order_id ?> — DroneDelivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
* { box-sizing: border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }
.page { max-width:1200px; margin:0 auto; padding:28px 28px 60px; }
.page-header { display:flex; align-items:center; gap:14px; margin-bottom:24px; }
.back-btn { display:inline-flex; align-items:center; gap:6px; color:#5a7a99; font-size:13px; padding:6px 12px; border:1px solid rgba(90,122,153,0.3); border-radius:8px; transition:0.15s; }
.back-btn:hover { color:#00eaff; border-color:rgba(0,234,255,0.3); }
.page-title { color:#00eaff; font-size:20px; font-weight:600; margin:0; }
.grid { display:grid; grid-template-columns:380px 1fr; gap:20px; align-items:start; }
.card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:14px; padding:22px; margin-bottom:16px; }
.card:last-child { margin-bottom:0; }
.card-title { color:#00eaff; font-size:13px; font-weight:600; letter-spacing:0.7px; text-transform:uppercase; margin:0 0 16px; padding-bottom:10px; border-bottom:1px solid rgba(0,234,255,0.08); }
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
.stat-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px; }
.stat-box { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.08); border-radius:10px; padding:12px 14px; }
.stat-label { font-size:11px; color:#5a7a99; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px; }
.stat-value { font-size:16px; font-weight:600; color:#00eaff; }
#map { height:420px; width:100%; border-radius:12px; border:1px solid rgba(0,234,255,0.1); }
</style>
</head>
<body>
<?php include "menu_customer.php"; ?>

<div class="page">
    <div class="page-header">
        <a href="orders_customer.php" class="back-btn">&#8592; I miei ordini</a>
        <h1 class="page-title">Ordine #<?= $order_id ?></h1>
        <span class="badge <?= $si['class'] ?>"><?= $si['label'] ?></span>
    </div>

    <div class="grid">
        <!-- SIDEBAR -->
        <div>
            <div class="card">
                <p class="card-title">Dettagli ordine</p>
                <div class="info-row"><span class="info-label">Ritiro</span><span class="info-value"><?= htmlspecialchars($order['pickup_address'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Consegna</span><span class="info-value"><?= htmlspecialchars($order['delivery_address'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Priorità</span><span class="info-value"><?= ucfirst($order['priority'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Creato il</span><span class="info-value"><?= $dateFmt ?></span></div>
                <?php if ($order['notes']): ?>
                <div class="info-row"><span class="info-label">Note</span><span class="info-value"><?= htmlspecialchars($order['notes']) ?></span></div>
                <?php endif; ?>
            </div>

            <div class="card">
                <p class="card-title">Timeline</p>
                <div class="timeline">
                    <div class="tl-step" id="step1"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">Ordine creato</div></div>
                    <div class="tl-step" id="step2"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">Drone assegnato</div></div>
                    <div class="tl-step" id="step3"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">In consegna</div></div>
                    <div class="tl-step" id="step4"><div class="tl-dot-wrap"><div class="tl-dot"></div><div class="tl-line"></div></div><div class="tl-text">Consegnato</div></div>
                </div>
            </div>
        </div>

        <!-- MAPPA -->
        <div>
            <div class="card" style="margin-bottom:16px;">
                <p class="card-title">Info consegna</p>
                <div class="stat-grid">
                    <div class="stat-box"><div class="stat-label">Drone</div><div class="stat-value" id="hud_drone">—</div></div>
                    <div class="stat-box"><div class="stat-label">Distanza</div><div class="stat-value" id="hud_distance">—</div></div>
                    <div class="stat-box"><div class="stat-label">Tempo stimato</div><div class="stat-value" id="hud_eta">—</div></div>
                    <div class="stat-box"><div class="stat-label">Stato</div><div class="stat-value" style="font-size:13px;"><?= $si['label'] ?></div></div>
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

const pickupLat  = parseFloat("<?= $order['pickup_lat']   ?? 0 ?>");
const pickupLng  = parseFloat("<?= $order['pickup_lng']   ?? 0 ?>");
const deliveryLat = parseFloat("<?= $order['delivery_lat'] ?? 0 ?>");
const deliveryLng = parseFloat("<?= $order['delivery_lng'] ?? 0 ?>");
const defaultCenter = [45.5416, 10.2118];

const map = L.map('map').setView(defaultCenter, 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19 }).addTo(map);

const pickup   = (!isNaN(pickupLat)   && pickupLat   !== 0) ? [pickupLat, pickupLng]     : null;
const delivery = (!isNaN(deliveryLat) && deliveryLat !== 0) ? [deliveryLat, deliveryLng] : null;

if (pickup) {
    L.marker(pickup).addTo(map).bindPopup("<b>Ritiro</b>");
}
if (delivery) {
    L.marker(delivery).addTo(map).bindPopup("<b>Consegna</b>");
}
if (pickup && delivery) {
    map.fitBounds(L.latLngBounds([pickup, delivery]), { padding: [40, 40] });
}

setTimeout(() => map.invalidateSize(), 300);

const drones = ["DRN‑01","DRN‑07","DRN‑12","DRN‑21"];
const drone = drones[Math.floor(Math.random() * drones.length)];
const dist  = (Math.random() * 4 + 1).toFixed(2);
const eta   = Math.round(dist * 3 + 2);
document.getElementById("hud_drone").textContent    = drone;
document.getElementById("hud_distance").textContent = dist + " km";
document.getElementById("hud_eta").textContent      = eta + " min";
</script>
</body>
</html>










