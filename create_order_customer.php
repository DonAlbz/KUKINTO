<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_customer.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pickup = trim($_POST['pickup']);
    $delivery = trim($_POST['delivery']);
    $priority = trim($_POST['priority']);
    $notes = trim($_POST['notes']);

    if ($pickup !== "" && $delivery !== "") {

        $stmt = $pdo->prepare("
            INSERT INTO orders (customer_id, pickup_address, delivery_address, priority, notes, status, created_at)
            VALUES (:cid, :p, :d, :prio, :n, 'in_attesa', NOW())
        ");

        $stmt->execute([
            ':cid' => $_SESSION['customer_id'],
            ':p'   => $pickup,
            ':d'   => $delivery,
            ':prio'=> $priority,
            ':n'   => $notes
        ]);

        header("Location: customer_dashboard.php");
        exit;

    } else {
        $message = "Compila tutti i campi obbligatori.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Crea nuovo ordine</title>

    <style>
        body {
            margin: 0;
            background: #0a0f1f;
            color: #e6f1ff;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .form-container {
            width: 90%;
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
            box-shadow: 0 0 20px #00eaff22;
        }

        h1 {
            text-align: center;
            color: #00eaff;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            color: #00eaff;
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #00eaff44;
            background: rgba(255, 255, 255, 0.05);
            color: #e6f1ff;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            background: #00eaff22;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.25s;
        }

        button:hover {
            background: #00eaff33;
            box-shadow: 0 0 10px #00eaff55;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #00eaff;
            text-decoration: none;
        }

        .back:hover {
            text-shadow: 0 0 6px #00eaff;
        }

        .error {
            color: #ff6b6b;
            text-align: center;
            margin-top: 15px;
        }
    </style>

</head>
<body>

<div class="form-container">

    <h1>Crea un nuovo ordine</h1>

    <form method="POST">

        <label>Indirizzo di ritiro *</label>
        <input type="text" name="pickup" required>

        <label>Indirizzo di consegna *</label>
        <input type="text" name="delivery" required>

        <label>Priorità</label>
        <select name="priority">
            <option value="bassa">Bassa</option>
            <option value="media">Media</option>
            <option value="alta">Alta</option>
        </select>

        <label>Note</label>
        <textarea name="notes" placeholder="Informazioni aggiuntive (opzionale)"></textarea>

        <button type="submit">Crea ordine</button>
    </form>

    <?php if ($message): ?>
        <p class="error"><?= $message ?></p>
    <?php endif; ?>

    <a href="customer_dashboard.php" class="back">← Torna alla dashboard</a>

</div>

</body>
</html>
