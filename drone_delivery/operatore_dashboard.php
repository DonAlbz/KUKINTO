<?php
require_once "auth_operatore.php";
require_once "config.php";

$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'in_consegna'");
$ordiniInCorso = (int)$stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Dashboard Operatore</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
* { box-sizing: border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
.page { max-width:1400px; margin:0 auto; padding:28px 28px 60px; }
.page-header { margin-bottom:24px; }
.page-title { color:#00eaff; font-size:22px; font-weight:600; margin:0 0 4px; }
.page-sub { color:#5a7a99; font-size:13px; }

/* STAT CARDS */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.stat-card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:12px; padding:18px 20px; transition:0.2s; }
.stat-card:hover { background:rgba(0,234,255,0.05); transform:translateY(-2px); }
.stat-label { font-size:11px; color:#5a7a99; text-transform:uppercase; letter-spacing:0.7px; margin-bottom:8px; }
.stat-value { font-size:28px; font-weight:600; color:#00eaff; }
.stat-card.s-attivo   .stat-value { color:#00e676; }
.stat-card.s-occupato .stat-value { color:#ffb74d; }
.stat-card.s-offline  .stat-value { color:#ff5252; }
.stat-card.s-ordini   .stat-value { color:#a78bfa; }

/* LAYOUT INFERIORE */
.bottom-grid { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }

/* CARD */
.card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:14px; padding:22px; }
.card-title { color:#00eaff; font-size:13px; font-weight:600; letter-spacing:0.7px; text-transform:uppercase; margin:0 0 16px; padding-bottom:10px; border-bottom:1px solid rgba(0,234,255,0.08); }

/* MAPPA */
#map { width:100%; height:460px; border-radius:10px; border:1px solid rgba(0,234,255,0.1); }
.legend { display:flex; gap:16px; margin-top:12px; }
.legend-item { display:flex; align-items:center; gap:6px; font-size:12px; color:#5a7a99; }
.dot { width:10px; height:10px; border-radius:50%; }
.dot-attivo   { background:#00e676; }
.dot-occupato { background:#ffb74d; }
.dot-offline  { background:#ff5252; }

/* ATTIVITÀ */
.activity-list { display:flex; flex-direction:column; gap:10px; }
.activity-item { padding:10px 12px; background:rgba(255,255,255,0.02); border:1px solid rgba(0,234,255,0.07); border-radius:8px; font-size:13px; }
.activity-time { font-size:11px; color:#5a7a99; margin-top:3px; }
.activity-dot { display:inline-block; width:7px; height:7px; border-radius:50%; margin-right:7px; vertical-align:middle; }

/* QUICK LINKS */
.quick-links { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:16px; }
.quick-link { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; padding:14px 8px; background:rgba(255,255,255,0.02); border:1px solid rgba(0,234,255,0.1); border-radius:10px; text-decoration:none; color:#e6f1ff; font-size:12px; transition:0.15s; }
.quick-link:hover { background:rgba(0,234,255,0.06); border-color:rgba(0,234,255,0.25); }
.quick-link-icon { font-size:20px; }
</style>
</head>
<body>
<?php include "menu_operatore.php"; ?>

<div class="page">
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <div class="page-sub">Benvenuto, <?= htmlspecialchars($_SESSION['username'] ?? 'Operatore') ?></div>
    </div>

    <div class="stats-grid">
        <div class="stat-card s-attivo">
            <div class="stat-label">Droni attivi</div>
            <div class="stat-value" id="stat-attivi">—</div>
        </div>
        <div class="stat-card s-occupato">
            <div class="stat-label">In consegna</div>
            <div class="stat-value" id="stat-occupati">—</div>
        </div>
        <div class="stat-card s-offline">
            <div class="stat-label">Offline</div>
            <div class="stat-value" id="stat-offline">—</div>
        </div>
        <div class="stat-card s-ordini">
            <div class="stat-label">Ordini in corso</div>
            <div class="stat-value"><?= $ordiniInCorso ?></div>
        </div>
    </div>

    <div class="bottom-grid">
        <!-- MAPPA -->
        <div class="card">
            <p class="card-title">Posizione droni in tempo reale</p>
            <div id="map"></div>
            <div class="legend">
                <div class="legend-item"><span class="dot dot-attivo"></span> Attivo</div>
                <div class="legend-item"><span class="dot dot-occupato"></span> In consegna</div>
                <div class="legend-item"><span class="dot dot-offline"></span> Offline</div>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div>
            <div class="card" style="margin-bottom:16px;">
                <p class="card-title">Accesso rapido</p>
                <div class="quick-links">
                    <a href="orders_operatore.php" class="quick-link">
                        <span class="quick-link-icon">📦</span> Ordini
                    </a>
                    <a href="drones.php" class="quick-link">
                        <span class="quick-link-icon">🚁</span> Droni
                    </a>
                    <a href="orders_operatore.php?filter=in_attesa" class="quick-link">
                        <span class="quick-link-icon">🎯</span> Da assegnare
                    </a>
                    <a href="add_drone.php" class="quick-link">
                        <span class="quick-link-icon">➕</span> Nuovo drone
                    </a>
                </div>
            </div>

            <div class="card">
                <p class="card-title">Ultime attività</p>
                <div class="activity-list" id="activity-log">
                    <div style="color:#5a7a99;font-size:13px;">Caricamento...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([45.5416, 10.2118], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19 }).addTo(map);

let droneMarkers = {};

function getColor(status) {
    if (status === 'attivo')   return '#00e676';
    if (status === 'occupato') return '#ffb74d';
    return '#ff5252';
}

function updateDashboard() {
    fetch('get_all_drones.php')
        .then(r => r.json())
        .then(drones => {
            let attivi=0, occupati=0, offline=0;

            drones.forEach(d => {
                let lat = parseFloat(d.latitude) || 45.5416;
                let lng = parseFloat(d.longitude) || 10.2118;
                const color = getColor(d.status);

                if (d.status === 'attivo')   attivi++;
                if (d.status === 'occupato') occupati++;
                if (d.status === 'offline')  offline++;

                const icon = L.divIcon({
                    className: '',
                    html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};box-shadow:0 0 10px ${color}88;border:2px solid #ffffff33;"></div>`
                });

                const key = 'drone_' + d.id;
                if (!droneMarkers[key]) {
                    droneMarkers[key] = L.marker([lat, lng], { icon }).addTo(map);
                } else {
                    droneMarkers[key].setLatLng([lat, lng]);
                    droneMarkers[key].setIcon(icon);
                }
                droneMarkers[key].bindPopup(`<strong>${d.name}</strong><br>Stato: ${d.status}<br>Batteria: ${d.battery}%`);
            });

            document.getElementById('stat-attivi').textContent   = attivi;
            document.getElementById('stat-occupati').textContent = occupati;
            document.getElementById('stat-offline').textContent  = offline;

            document.getElementById('activity-log').innerHTML =
                `<div class="activity-item">
                    <span class="activity-dot" style="background:#00e676"></span>
                    ${attivi} droni attivi, ${occupati} in consegna
                    <div class="activity-time">${new Date().toLocaleTimeString('it-IT')}</div>
                </div>`;
        })
        .catch(() => {});
}

updateDashboard();
setInterval(updateDashboard, 3000);
</script>
</body>
</html>