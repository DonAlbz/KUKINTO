<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "auth_customer.php";
require_once "config.php";

$customer_id = $_SESSION['customer_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = :cid ORDER BY created_at DESC");
$stmt->execute([':cid' => $customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>I miei ordini — DroneDelivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
* { box-sizing: border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }
.page { max-width:1400px; margin:0 auto; padding:28px 28px 60px; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.page-title { color:#00eaff; font-size:22px; font-weight:600; margin:0; }
.count-pill { background:rgba(0,234,255,0.08); border:1px solid rgba(0,234,255,0.2); color:#00eaff; font-size:12px; padding:4px 12px; border-radius:999px; }
.btn-new { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:500; color:#00eaff; border:1px solid rgba(0,234,255,0.3); background:rgba(0,234,255,0.06); transition:0.15s; }
.btn-new:hover { filter:brightness(1.2); }
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:separate; border-spacing:0 6px; min-width:700px; }
thead th { font-size:11px; font-weight:600; letter-spacing:0.8px; text-transform:uppercase; color:#5a7a99; padding:0 16px 8px; text-align:left; border-bottom:1px solid rgba(0,234,255,0.08); }
tbody tr { background:rgba(255,255,255,0.03); transition:background 0.15s; }
tbody tr:hover { background:rgba(0,234,255,0.05); }
tbody td { padding:14px 16px; color:#cfe8ff; font-size:13.5px; vertical-align:middle; }
tbody td:first-child { border-radius:10px 0 0 10px; }
tbody td:last-child  { border-radius:0 10px 10px 0; }
.order-id { color:#5a7a99; font-size:12px; font-weight:600; font-family:monospace; }
.address  { color:#8aaec8; font-size:13px; max-width:220px; line-height:1.4; }
.date     { color:#5a7a99; font-size:12px; line-height:1.5; }
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
.empty { padding:40px; text-align:center; color:#5a7a99; }
</style>
</head>
<body>
<?php include "menu_customer.php"; ?>

<div class="page">
    <div class="page-header">
        <div style="display:flex;align-items:center;gap:12px;">
            <h1 class="page-title">I miei ordini</h1>
            <span class="count-pill"><?= count($orders) ?> ordini</span>
        </div>
        <a href="create_order_customer.php" class="btn-new">+ Nuovo ordine</a>
    </div>

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
                    'in_attesa'   => ['label' => 'In attesa',   'class' => 'b-attesa'],
                    'in_consegna' => ['label' => 'In consegna', 'class' => 'b-consegna'],
                    'completato'  => ['label' => 'Completato',  'class' => 'b-completato'],
                    'annullato'   => ['label' => 'Annullato',   'class' => 'b-annullato'],
                ];
                $si = $statusMap[$o['status']] ?? ['label' => ucfirst($o['status']), 'class' => 'b-attesa'];
                $dateFmt = date('d M Y', strtotime($o['created_at']));
                $timeFmt = date('H:i', strtotime($o['created_at']));
            ?>
            <tr>
                <td><span class="order-id">#<?= $o['id'] ?></span></td>
                <td><span class="address"><?= htmlspecialchars($o['pickup_address'] ?? '—') ?></span></td>
                <td><span class="address"><?= htmlspecialchars($o['delivery_address'] ?? '—') ?></span></td>
                <td><span class="badge <?= $si['class'] ?>"><?= $si['label'] ?></span></td>
                <td><div class="date"><?= $dateFmt ?><br><?= $timeFmt ?></div></td>
                <td><a href="order_details_customer.php?id=<?= $o['id'] ?>" class="btn-detail">Dettagli</a></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
