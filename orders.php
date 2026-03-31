<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

$userRole = $_SESSION['role'];
$userId   = $_SESSION['user_id'];

// Recupero ordini
if ($userRole === 'cliente') {
    $stmt = $pdo->prepare("
        SELECT 
            o.*,
            c.username AS customer_name
        FROM orders o
        LEFT JOIN customers c ON c.id = o.customer_id
        WHERE o.customer_id = :cid
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([':cid' => $userId]);
} else {
    $stmt = $pdo->query("
        SELECT 
            o.*,
            c.username AS customer_name
        FROM orders o
        LEFT JOIN customers c ON c.id = o.customer_id
        ORDER BY o.created_at DESC
    ");
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Ordini</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
        /* ============================
           LAYOUT ORDERS
        ============================ */

        .orders-container {
            width: 90%;
            margin: 40px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #00eaff;
        }

        .role-info {
            text-align: center;
            margin-bottom: 20px;
            opacity: 0.85;
        }

        .btn-new {
            display: inline-block;
            padding: 10px 18px;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            background: rgba(0, 234, 255, 0.04);
            transition: 0.25s;
        }

        .btn-new:hover {
            background: rgba(0, 234, 255, 0.12);
            box-shadow: 0 0 10px #00eaff55;
        }

        /* Badge stato */
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .status-in_attesa { background: #ffcc0044; color: #ffcc00; }
        .status-in_consegna { background: #00eaff44; color: #00eaff; }
        .status-completato { background: #00ff8844; color: #00ff88; }
        .status-annullato { background: #ff444444; color: #ff4444; }

        .btn-details {
            color: #00eaff;
            text-decoration: none;
            font-weight: 500;
            transition: 0.25s;
        }

        .btn-details:hover {
            text-shadow: 0 0 6px #00eaff;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="orders-container">

    <h1>Gestione Ordini</h1>

    <div style="text-align:right; margin-bottom:20px;">
        <a href="create_order.php" class="btn-new">+ Nuovo Ordine</a>
    </div>

    <p class="role-info">
        <?php if ($userRole === 'cliente'): ?>
            Sei un <strong>cliente</strong>: puoi vedere solo i tuoi ordini.
        <?php elseif ($userRole === 'operatore'): ?>
            Sei un <strong>operatore</strong>: puoi vedere tutti gli ordini.
        <?php elseif ($userRole === 'admin'): ?>
            Sei un <strong>admin</strong>: puoi vedere e gestire tutti gli ordini.
        <?php endif; ?>
    </p>

    <div class="card">
        <table>
            <tr>
                <th>ID Ordine</th>
                <th>Cliente</th>
                <th>Ritiro</th>
                <th>Consegna</th>
                <th>Priorità</th>
                <th>Status</th>
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
                <td>
                    <span class="status-badge status-<?= $o['status'] ?>">
                        <?= $o['status'] ?>
                    </span>
                </td>
                <td><?= $o['created_at'] ?></td>
                <td>
                    <a href="order_details.php?id=<?= $o['id'] ?>" class="btn-details">Apri</a>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>

</div>

</body>
</html>

