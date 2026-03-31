<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

$userRole = $_SESSION['role'];
$userId   = $_SESSION['user_id'];

$message = "";

// Se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Se cliente → può creare solo per sé
    if ($userRole === 'cliente') {
        $orderUserId = $userId;
    } else {
        $orderUserId = $_POST['user_id'];
    }

    $address      = $_POST['address'];
    $packageType  = $_POST['package_type'];
    $weight       = $_POST['weight'];
    $notes        = $_POST['notes'];
    $priority     = $_POST['priority'];

    // Inserimento ordine
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, address, package_type, weight, notes, priority, status, created_at)
        VALUES (:uid, :addr, :ptype, :w, :n, :p, 'in_attesa', NOW())
    ");

    $stmt->execute([
        ':uid'   => $orderUserId,
        ':addr'  => $address,
        ':ptype' => $packageType,
        ':w'     => $weight,
        ':n'     => $notes,
        ':p'     => $priority
    ]);

    header("Location: orders.php");
    exit;
}

// Recupera utenti (solo per admin/operatori)
if ($userRole !== 'cliente') {
    $users = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Crea Ordine</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
        /* ============================
           LAYOUT FORM CREAZIONE ORDINE
        ============================ */

        .form-container {
            width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #00eaff;
        }

        label {
            color: #e6f1ff;
            font-size: 14px;
            opacity: 0.85;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: 1px solid #00eaff;
            background: rgba(0, 234, 255, 0.04);
            color: #00eaff;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.25s;
        }

        button:hover {
            background: rgba(0, 234, 255, 0.12);
            box-shadow: 0 0 10px #00eaff55;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #00eaff;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-shadow: 0 0 6px #00eaff;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="form-container">

    <h1>Crea un nuovo ordine</h1>

    <form method="POST">

        <?php if ($userRole !== 'cliente'): ?>
            <label>Utente</label>
            <select name="user_id" required>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= $u['username'] ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p style="text-align:center; opacity:0.8;">L’ordine sarà creato per il tuo account.</p>
        <?php endif; ?>

        <input type="text" name="address" placeholder="Indirizzo di consegna" required>

        <input type="text" name="package_type" placeholder="Tipo di pacco (es. documenti, elettronica...)" required>

        <input type="number" step="0.01" name="weight" placeholder="Peso (kg)" required>

        <label>Priorità</label>
        <select name="priority" required>
            <option value="bassa">Bassa</option>
            <option value="media" selected>Media</option>
            <option value="alta">Alta</option>
        </select>

        <textarea name="notes" placeholder="Note aggiuntive"></textarea>

        <button type="submit">Crea Ordine</button>
    </form>

    <a href="orders.php" class="back-link">⬅ Torna agli ordini</a>

</div>

</body>
</html>
