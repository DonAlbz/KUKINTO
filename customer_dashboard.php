<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_customer.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = :cid ORDER BY created_at DESC");
$stmt->execute([':cid' => $customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Cliente</title>

    <style>
        body {
            margin: 0;
            background: #0a0f1f;
            color: #e6f1ff;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .dashboard-container {
            width: 90%;
            max-width: 900px;
            margin: 60px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
            box-shadow: 0 0 20px #00eaff22;
        }

        h1, h2 {
            color: #00eaff;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            opacity: 0.85;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.25s;
            background: rgba(0, 234, 255, 0.05);
        }

        .btn:hover {
            background: rgba(0, 234, 255, 0.15);
            box-shadow: 0 0 10px #00eaff55;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #00eaff22;
        }

        th {
            background: rgba(0, 234, 255, 0.08);
            color: #00eaff;
            font-weight: 600;
        }

        tr:hover {
            background: rgba(0, 234, 255, 0.05);
        }

        .logout {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #ff6b6b;
            text-decoration: none;
        }

        .logout:hover {
            text-shadow: 0 0 6px #ff6b6b;
        }
    </style>

</head>
<body>

<div class="dashboard-container">

    <h1>Ciao <?= htmlspecialchars($_SESSION['customer_username']) ?></h1>
    <p>Benvenuto nella tua area personale. Qui puoi gestire i tuoi ordini.</p>

    <div style="text-align:center;">
        <a href="create_order_customer.php" class="btn">+ Crea un nuovo ordine</a>
    </div>

    <h2>I tuoi ordini</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Ritiro</th>
            <th>Consegna</th>
            <th>Stato</th>
            <th>Data</th>
            <th>Dettagli</th>
        </tr>

        <?php if (count($orders) === 0): ?>
            <tr>
                <td colspan="6" style="padding:20px; opacity:0.7;">Nessun ordine presente.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['pickup_address']) ?></td>
            <td><?= htmlspecialchars($order['delivery_address']) ?></td>
            <td><?= $order['status'] ?></td>
            <td><?= $order['created_at'] ?></td>
            <td><a class="btn" href="order_details_customer.php?id=<?= $order['id'] ?>">Apri</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a href="logout.php" class="logout">Disconnetti</a>

</div>

</body>
</html>

