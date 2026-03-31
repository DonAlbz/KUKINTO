<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

// =========================
// STATISTICHE ORDINI
// =========================

$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'in_attesa'")->fetchColumn();
$inDeliveryOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'in_consegna'")->fetchColumn();
$completedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'completato'")->fetchColumn();

// =========================
// STATISTICHE DRONI
// =========================

$totalDrones = $pdo->query("SELECT COUNT(*) FROM drones")->fetchColumn();
$activeDrones = $pdo->query("SELECT COUNT(*) FROM drones WHERE status='attivo'")->fetchColumn();
$offlineDrones = $pdo->query("SELECT COUNT(*) FROM drones WHERE status='offline'")->fetchColumn();

// =========================
// ULTIMI ORDINI
// =========================

$stmt = $pdo->query("
    SELECT 
        o.id,
        o.pickup_address,
        o.delivery_address,
        o.priority,
        o.status,
        o.created_at,
        c.username AS customer_name
    FROM orders o
    LEFT JOIN customers c ON c.id = o.customer_id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
        /* ============================
           LAYOUT DASHBOARD
        ============================ */

        .dashboard-container {
            width: 90%;
            margin: 40px auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            transition: 0.25s;
        }

        .stat-card:hover {
            background: rgba(0, 234, 255, 0.08);
            box-shadow: 0 0 12px #00eaff33;
        }

        .stat-card h3 {
            margin-bottom: 10px;
            color: #00eaff;
        }

        .stat-card p {
            font-size: 26px;
            font-weight: bold;
        }

        .latest-orders {
            margin-top: 50px;
        }

        .latest-orders h3 {
            color: #00eaff;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Badge stato */
        .status-badge {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: bold;
        }

        .status-in_attesa { background: #ffcc0044; color: #ffcc00; }
        .status-in_consegna { background: #00eaff44; color: #00eaff; }
        .status-completato { background: #00ff8844; color: #00ff88; }

        /* Pulsante dettagli */
        .btn-primary {
            display: inline-block;
            padding: 8px 14px;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.25s;
        }

        .btn-primary:hover {
            background: rgba(0, 234, 255, 0.12);
            box-shadow: 0 0 10px #00eaff55;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="dashboard-container">

    <h1 style="text-align:center; margin-bottom:40px;">Dashboard</h1>

    <!-- STATISTICHE -->
    <div class="stats-grid">

        <div class="stat-card">
            <h3>Ordini Totali</h3>
            <p><?= $totalOrders ?></p>
        </div>

        <div class="stat-card">
            <h3>In Attesa</h3>
            <p><?= $pendingOrders ?></p>
        </div>

        <div class="stat-card">
            <h3>In Consegna</h3>
            <p><?= $inDeliveryOrders ?></p>
        </div>

        <div class="stat-card">
            <h3>Completati</h3>
            <p><?= $completedOrders ?></p>
        </div>

        <div class="stat-card">
            <h3>Droni Totali</h3>
            <p><?= $totalDrones ?></p>
        </div>

        <div class="stat-card">
            <h3>Droni Attivi</h3>
            <p><?= $activeDrones ?></p>
        </div>

    </div>

    <!-- ULTIMI ORDINI -->
    <div class="latest-orders">
        <h3>Ultimi Ordini</h3>

        <div class="card">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Ritiro</th>
                    <th>Consegna</th>
                    <th>Priorità</th>
                    <th>Stato</th>
                    <th>Creato il</th>
                    <th>Dettagli</th>
                </tr>

                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['customer_name'] ?? 'Sconosciuto') ?></td>
                    <td><?= htmlspecialchars($o['pickup_address']) ?></td>
                    <td><?= htmlspecialchars($o['delivery_address']) ?></td>
                    <td><?= htmlspecialchars($o['priority']) ?></td>
                    <td><span class="status-badge status-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                    <td><?= $o['created_at'] ?></td>
                    <td>
                        <a href="order_details.php?id=<?= $o['id'] ?>" class="btn-primary">Apri</a>
                    </td>
                </tr>
                <?php endforeach; ?>

            </table>
        </div>
    </div>

</div>

<script>
setInterval(() => {
    fetch("simulate_drones.php");
}, 3000);
</script>

</body>
</html>
