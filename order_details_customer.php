<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_customer.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_GET['id'])) {
    header("Location: customer_dashboard.php");
    exit;
}

$order_id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT * FROM orders 
    WHERE id = :oid AND customer_id = :cid
    LIMIT 1
");
$stmt->execute([
    ':oid' => $order_id,
    ':cid' => $customer_id
]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: customer_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dettagli Ordine #<?= $order['id'] ?></title>

    <style>
        body {
            margin: 0;
            background: #0a0f1f;
            color: #e6f1ff;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .details-container {
            width: 90%;
            max-width: 700px;
            margin: 60px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
            box-shadow: 0 0 20px #00eaff22;
        }

        h1 {
            color: #00eaff;
            text-align: center;
            margin-bottom: 25px;
        }

        .field {
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid #00eaff22;
        }

        .label {
            font-weight: 600;
            color: #00eaff;
            font-size: 15px;
        }

        .value {
            display: block;
            margin-top: 4px;
            opacity: 0.9;
            font-size: 17px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 25px;
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
    </style>
</head>
<body>

<div class="details-container">
    <h1>Dettagli ordine #<?= $order['id'] ?></h1>

    <div class="field">
        <span class="label">Ritiro:</span>
        <span class="value"><?= htmlspecialchars($order['pickup_address']) ?></span>
    </div>

    <div class="field">
        <span class="label">Consegna:</span>
        <span class="value"><?= htmlspecialchars($order['delivery_address']) ?></span>
    </div>

    <div class="field">
        <span class="label">Priorità:</span>
        <span class="value"><?= htmlspecialchars($order['priority']) ?></span>
    </div>

    <div class="field">
        <span class="label">Note:</span>
        <span class="value"><?= nl2br(htmlspecialchars($order['notes'])) ?></span>
    </div>

    <div class="field">
        <span class="label">Stato:</span>
        <span class="value"><?= htmlspecialchars($order['status']) ?></span>
    </div>

    <div class="field">
        <span class="label">Creato il:</span>
        <span class="value"><?= $order['created_at'] ?></span>
    </div>

    <a href="customer_dashboard.php" class="btn">← Torna alla dashboard</a>
</div>

</body>
</html>

</html>
