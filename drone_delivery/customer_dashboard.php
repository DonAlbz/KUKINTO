<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "auth_customer.php";
require_once "config.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_customer.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

$stmtUser = $pdo->prepare("SELECT username FROM customers WHERE id = :id LIMIT 1");
$stmtUser->execute([':id' => $customer_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$customer_name = $user['username'] ?? "Utente";

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = :cid ORDER BY created_at DESC");
$stmt->execute([':cid' => $customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$in_attesa = 0; $in_consegna = 0; $completati = 0; $annullati = 0;
foreach ($orders as $o) {
    if ($o['status'] === 'in_attesa')   $in_attesa++;
    if ($o['status'] === 'in_consegna') $in_consegna++;
    if ($o['status'] === 'completato')  $completati++;
    if ($o['status'] === 'annullato')   $annullati++;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>La mia area — DroneDelivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
* { box-sizing: border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }

/* PAGE */
.page { max-width:1200px; margin:0 auto; padding:28px 28px 60px; }

/* HERO */
.hero { margin-bottom:24px; }
.hero-title { font-size:22px; font-weight:600; color:#00eaff; margin:0 0 4px; }
.hero-sub { font-size:13px; color:#5a7a99; }

/* STATS */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.stat-card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:12px; padding:18px 20px; }
.stat-label { font-size:11px; color:#5a7a99; text-transform:uppercase; letter-spacing:0.7px; margin-bottom:8px; }
.stat-value { font-size:28px; font-weight:600; color:#00eaff; }
.stat-card.s-attesa    .stat-value { color:#90caf9; }
.stat-card.s-consegna  .stat-value { color:#ffb74d; }
.stat-card.s-completato .stat-value { color:#00e676; }
.stat-card.s-annullato  .stat-value { color:#ff5252; }

/* LAYOUT */
.main-grid { display:grid; grid-template-columns:1fr 260px; gap:20px; align-items:start; }

/* CARD */
.card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:14px; padding:22px; margin-bottom:16px; }
.card:last-child { margin-bottom:0; }
.card-title { color:#00eaff; font-size:13px; font-weight:600; letter-spacing:0.7px; text-transform:uppercase; margin:0 0 16px; padding-bottom:10px; border-bottom:1px solid rgba(0,234,255,0.08); }

/* TABELLA */
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:separate; border-spacing:0 6px; min-width:600px; }
thead th { font-size:11px; font-weight:600; letter-spacing:0.8px; text-transform:uppercase; color:#5a7a99; padding:0 14px 8px; text-align:left; border-bottom:1px solid rgba(0,234,255,0.08); }
tbody tr { background:rgba(255,255,255,0.02); transition:0.15s; }
tbody tr:hover { background:rgba(0,234,255,0.04); }
tbody td { padding:12px 14px; font-size:13.5px; color:#cfe8ff; vertical-align:middle; }
tbody td:first-child { border-radius:10px 0 0 10px; }
tbody td:last-child  { border-radius:0 10px 10px 0; }
.order-id { color:#5a7a99; font-size:12px; font-family:monospace; font-weight:600; }
.address  { color:#8aaec8; font-size:13px; max-width:180px; }
.date     { color:#5a7a99; font-size:12px; }

/* BADGE */
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

.btn-detail { display:inline-block; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; color:#00eaff; border:1px solid rgba(0,234,255,0.25); background:rgba(0,234,255,0.06); transition:0.15s; }
.btn-detail:hover { filter:brightness(1.2); }
.empty { padding:30px; text-align:center; color:#5a7a99; font-size:13px; }

/* CHART */
.chart-wrap { display:flex; justify-content:center; padding:10px 0; }
</style>
</head>
<body>

<body>
<?php include "menu_customer.php"; ?>

<div class="page">
    <div class="hero">
        <div class="hero-title">Ciao, <?= htmlspecialchars($customer_name) ?> 👋</div>
        <div class="hero-sub">Bentornato nella tua area personale</div>
    </div>

    <div class="stats-grid">
        <div class="stat-card s-attesa">
            <div class="stat-label">In attesa</div>
            <div class="stat-value"><?= $in_attesa ?></div>
        </div>
        <div class="stat-card s-consegna">
            <div class="stat-label">In consegna</div>
            <div class="stat-value"><?= $in_consegna ?></div>
        </div>
        <div class="stat-card s-completato">
            <div class="stat-label">Completati</div>
            <div class="stat-value"><?= $completati ?></div>
        </div>
        <div class="stat-card s-annullato">
            <div class="stat-label">Annullati</div>
            <div class="stat-value"><?= $annullati ?></div>
        </div>
    </div>

    <div class="main-grid">
        <!-- TABELLA ORDINI -->
        <div class="card">
            <p class="card-title">I miei ordini</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ritiro</th>
                            <th>Consegna</th>
                            <th>Stato</th>
                            <th>Data</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="empty">Nessun ordine ancora — <a href="create_order_customer.php" style="color:#00eaff;">crea il primo!</a></td></tr>
                    <?php else: foreach ($orders as $o):
                        $statusMap = [
                            'in_attesa'   => ['label'=>'In attesa',   'class'=>'b-attesa'],
                            'in_consegna' => ['label'=>'In consegna', 'class'=>'b-consegna'],
                            'completato'  => ['label'=>'Completato',  'class'=>'b-completato'],
                            'annullato'   => ['label'=>'Annullato',   'class'=>'b-annullato'],
                        ];
                        $si = $statusMap[$o['status']] ?? ['label'=>ucfirst($o['status']),'class'=>'b-attesa'];
                        $dateFmt = date('d M Y', strtotime($o['created_at']));
                    ?>
                    <tr>
                        <td><span class="order-id">#<?= $o['id'] ?></span></td>
                        <td><span class="address"><?= htmlspecialchars($o['pickup_address'] ?? '—') ?></span></td>
                        <td><span class="address"><?= htmlspecialchars($o['delivery_address'] ?? '—') ?></span></td>
                        <td><span class="badge <?= $si['class'] ?>"><?= $si['label'] ?></span></td>
                        <td><span class="date"><?= $dateFmt ?></span></td>
                        <td><a href="order_details_customer.php?id=<?= $o['id'] ?>" class="btn-detail">Dettagli</a></td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div>
            <div class="card">
                <p class="card-title">Riepilogo</p>
                <div class="chart-wrap">
                    <canvas id="ordersChart" width="200" height="200"></canvas>
                </div>
            </div>

            <div class="card">
                <p class="card-title">Azioni rapide</p>
                <a href="create_order_customer.php" style="display:block;padding:10px 14px;margin-bottom:8px;border-radius:8px;font-size:13px;font-weight:500;color:#00eaff;border:1px solid rgba(0,234,255,0.25);background:rgba(0,234,255,0.06);text-align:center;">+ Nuovo ordine</a>
                <a href="customer_logout.php" style="display:block;padding:10px 14px;border-radius:8px;font-size:13px;font-weight:500;color:#ff5252;border:1px solid rgba(255,82,82,0.25);background:rgba(255,82,82,0.06);text-align:center;">Esci</a>
            </div>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('ordersChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['In attesa', 'In consegna', 'Completati', 'Annullati'],
        datasets: [{
            data: [<?= $in_attesa ?>, <?= $in_consegna ?>, <?= $completati ?>, <?= $annullati ?>],
            backgroundColor: ['#90caf9','#ffb74d','#00e676','#ff5252'],
            borderColor: '#0a0f1f',
            borderWidth: 3
        }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { labels: { color:'#8aaec8', font:{ size:11 }, boxWidth:10 } } }
    }
});
</script>
</body>
</html>







