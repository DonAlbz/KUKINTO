<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

if (!isset($_GET['id'])) {
    die("ID ordine mancante.");
}

$orderId = intval($_GET['id']);

// Recupero ordine + nome cliente
$stmt = $pdo->prepare("
    SELECT 
        o.*,
        c.username AS customer_name
    FROM orders o
    LEFT JOIN customers c ON c.id = o.customer_id
    WHERE o.id = :id
");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Ordine non trovato.");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dettagli Ordine</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
/* ============================
   DETTAGLI ORDINE — VERSIONE COMPATTA E SIMMETRICA
============================ */

.details-container {
    width: 90%;
    max-width: 900px;
    margin: 40px auto;
}

h1 {
    text-align: center;
    margin-bottom: 25px;
    color: #00eaff;
}

/* GRIGLIA COMPATTA E SIMMETRICA */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 14px 16px; /* molto più compatto */
    margin-top: 10px;
}

/* BOX PIÙ COMPATTI + ICONE */
.detail-box {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid #00eaff22;
    padding: 16px 18px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    position: relative;
}

/* ICONA NEON */
.detail-box::before {
    content: attr(data-icon);
    position: absolute;
    top: 10px;
    right: 12px;
    font-size: 18px;
    opacity: 0.6;
    color: #00eaff;
}

/* TITOLI */
.detail-box h4 {
    margin: 0;
    color: #00eaff;
    font-size: 16px;
    font-weight: 600;
}

/* TESTO */
.detail-box p {
    margin: 0;
    font-size: 15px;
    opacity: 0.85;
}

/* BADGE STATO */
.status-badge {
    padding: 6px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
}

.status-in_attesa { background: #ffcc0044; color: #ffcc00; }
.status-in_consegna { background: #00eaff44; color: #00eaff; }
.status-completato { background: #00ff8844; color: #00ff88; }
.status-annullato { background: #ff444444; color: #ff4444; }

/* LINK INDIETRO */
.btn-back {
    display: block;
    text-align: center;
    margin-top: 25px;
    color: #00eaff;
    text-decoration: none;
    font-weight: 500;
}

.btn-back:hover {
    text-shadow: 0 0 6px #00eaff;
}

    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="details-container">

    <h1>Dettagli Ordine #<?= $order['id'] ?></h1>
<div class="details-grid">

    <div class="detail-box" data-icon="👤">
        <h4>Cliente</h4>
        <p><?= htmlspecialchars($order['customer_name'] ?? 'Sconosciuto') ?></p>
    </div>

    <div class="detail-box" data-icon="📦">
        <h4>Indirizzo di ritiro</h4>
        <p><?= htmlspecialchars($order['pickup_address']) ?></p>
    </div>

    <div class="detail-box" data-icon="📍">
        <h4>Indirizzo di consegna</h4>
        <p><?= htmlspecialchars($order['delivery_address']) ?></p>
    </div>

    <div class="detail-box" data-icon="⚡">
        <h4>Priorità</h4>
        <p><?= htmlspecialchars($order['priority']) ?></p>
    </div>

    <div class="detail-box" data-icon="⏳">
        <h4>Stato</h4>
        <p>
            <span class="status-badge status-<?= $order['status'] ?>">
                <?= $order['status'] ?>
            </span>
        </p>
    </div>

    <div class="detail-box" data-icon="📝">
        <h4>Note</h4>
        <p><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
    </div>

    <div class="detail-box" data-icon="📅">
        <h4>Creato il</h4>
        <p><?= $order['created_at'] ?></p>
    </div>

</div>


    <a href="orders.php" class="btn-back">⬅ Torna agli Ordini</a>

</div>

</body>
</html>
