<?php
require_once "auth_operatore.php";
require_once "config.php";

if (!isset($_SESSION['operator_id'])) {
    header("Location: login_operatore.php");
    exit;
}

$orders = [];
$errorMsg = "";

try {
    $stmt = $pdo->prepare("SELECT o.*, c.username AS cliente_username
                           FROM orders o
                           LEFT JOIN customers c ON o.customer_id = c.id
                           ORDER BY o.created_at DESC
                           LIMIT 200");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!is_array($orders)) $orders = [];
} catch (Exception $e) {
    $errorMsg = "Errore nella query ordini: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Ordini Operatore</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
.container { max-width:1600px; margin:32px auto; padding:0 24px 60px; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
.page-title { color:#00eaff; font-size:22px; font-weight:600; margin:0; letter-spacing:0.3px; }
.count-pill { background:rgba(0,234,255,0.08); border:1px solid rgba(0,234,255,0.2); color:#00eaff; font-size:12px; padding:4px 12px; border-radius:999px; }
.table-wrap { overflow-x:auto; }
table.orders { width:100%; border-collapse:separate; border-spacing:0 6px; min-width:860px; }
table.orders thead th { font-size:11px; font-weight:600; letter-spacing:0.8px; text-transform:uppercase; color:#5a7a99; padding:0 16px 8px; text-align:left; border-bottom:1px solid rgba(0,234,255,0.08); }
table.orders tbody tr { background:rgba(255,255,255,0.03); transition:background 0.15s; }
table.orders tbody tr:hover { background:rgba(0,234,255,0.05); }
table.orders tbody td { padding:14px 16px; color:#cfe8ff; font-size:13.5px; vertical-align:middle; }
table.orders tbody td:first-child { border-radius:10px 0 0 10px; }
table.orders tbody td:last-child { border-radius:0 10px 10px 0; }
.order-id { color:#5a7a99; font-size:12px; font-weight:600; font-family:monospace; }
.client-name { font-weight:500; color:#e6f1ff; }
.address { color:#8aaec8; font-size:13px; line-height:1.4; }
.badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
.badge::before { content:''; width:6px; height:6px; border-radius:50%; }
.b-attesa { background:rgba(144,202,249,0.12); color:#90caf9; border:1px solid rgba(144,202,249,0.25); }
.b-attesa::before { background:#90caf9; }
.b-consegna { background:rgba(255,183,77,0.12); color:#ffb74d; border:1px solid rgba(255,183,77,0.25); }
.b-consegna::before { background:#ffb74d; }
.b-completato { background:rgba(0,230,118,0.10); color:#00e676; border:1px solid rgba(0,230,118,0.20); }
.b-completato::before { background:#00e676; }
.b-annullato { background:rgba(255,82,82,0.10); color:#ff5252; border:1px solid rgba(255,82,82,0.20); }
.b-annullato::before { background:#ff5252; }
.battery { display:flex; align-items:center; gap:8px; }
.battery-bar { width:48px; height:6px; background:rgba(255,255,255,0.08); border-radius:3px; overflow:hidden; }
.battery-fill { height:100%; border-radius:3px; }
.date { color:#5a7a99; font-size:12px; line-height:1.5; }
.actions { display:flex; align-items:center; gap:6px; }
.btn-action { display:inline-block; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; text-decoration:none; border:1px solid; }
.btn-detail { color:#00eaff; border-color:rgba(0,234,255,0.25); background:rgba(0,234,255,0.06); }
.btn-assign { color:#a78bfa; border-color:rgba(167,139,250,0.25); background:rgba(167,139,250,0.06); }
.btn-action:hover { filter:brightness(1.2); }
.error { margin-bottom:16px; color:#ffb3b3; background:rgba(255,0,0,0.05); padding:12px 16px; border-radius:8px; border:1px solid rgba(255,77,77,0.2); }
.empty { padding:40px; text-align:center; color:#5a7a99; }
</style>
</head>
<body>
<?php include "menu_operatore.php"; ?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Ordini</h1>
        <span class="count-pill"><?= count($orders) ?> ordini</span>
    </div>

    <?php if ($errorMsg): ?>
        <div class="error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="orders">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Indirizzo consegna</th>
                    <th>Stato</th>
                    <th>Batteria</th>
                    <th>Creato il</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="7" class="empty">Nessun ordine trovato</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $o):
                    $status = strtolower($o['status'] ?? 'in_attesa');
                    $badgeClass = 'b-attesa';
                    $badgeLabel = 'In attesa';
                    if ($status === 'in_consegna') { $badgeClass = 'b-consegna';   $badgeLabel = 'In consegna'; }
                    if ($status === 'completato')  { $badgeClass = 'b-completato'; $badgeLabel = 'Completato'; }
                    if ($status === 'annullato')   { $badgeClass = 'b-annullato';  $badgeLabel = 'Annullato'; }

                    $battery = $o['battery_required'] ?? null;
                    $battPct = is_numeric($battery) ? (int)$battery : 0;
                    $battColor = $battPct >= 60 ? '#00e676' : ($battPct >= 30 ? '#ffb74d' : '#ff5252');

                    $date = $o['created_at'] ?? '';
                    $dateFmt = $date ? date('d M Y', strtotime($date)) : '—';
                    $timeFmt = $date ? date('H:i', strtotime($date)) : '';
                ?>
                <tr>
                    <td><span class="order-id">#<?= htmlspecialchars($o['id']) ?></span></td>
                    <td><span class="client-name"><?= htmlspecialchars($o['cliente_username'] ?? 'Anonimo') ?></span></td>
                    <td><span class="address"><?= htmlspecialchars($o['delivery_address'] ?? '—') ?></span></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                    <td>
                        <div class="battery">
                            <div class="battery-bar">
                                <div class="battery-fill" style="width:<?= $battPct ?>%;background:<?= $battColor ?>"></div>
                            </div>
                            <span style="font-size:12px;color:#8aaec8"><?= $battery !== null ? $battPct.'%' : '—' ?></span>
                        </div>
                    </td>
                    <td><div class="date"><?= $dateFmt ?><br><?= $timeFmt ?></div></td>
                    <td>
                        <div class="actions">
                            <a class="btn-action btn-detail" href="order_details_operatore.php?id=<?= urlencode($o['id']) ?>">Dettaglio</a>
                            <a class="btn-action btn-assign" href="assign_drone.php?id=<?= urlencode($o['id']) ?>">Assegna</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>




