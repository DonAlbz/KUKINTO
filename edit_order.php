<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

if (!isset($_GET['id'])) {
    die("ID ordine mancante.");
}

$id = $_GET['id'];
$userRole = $_SESSION['role'];
$userId   = $_SESSION['user_id'];

// Recupera l'ordine
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Ordine non trovato.");
}

// Se cliente → può modificare solo i suoi ordini
if ($userRole === 'cliente' && $order['user_id'] != $userId) {
    die("Non hai i permessi per modificare questo ordine.");
}

// Recupera droni
$drones = $pdo->query("SELECT id, name FROM drones ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        UPDATE orders SET
            address = :a,
            package_type = :p,
            weight = :w,
            notes = :n,
            priority = :pr,
            status = :s,
            drone_id = :d
        WHERE id = :id
    ");

    $stmt->execute([
        ':a' => $_POST['address'],
        ':p' => $_POST['package_type'],
        ':w' => $_POST['weight'],
        ':n' => $_POST['notes'],
        ':pr' => $_POST['priority'],
        ':s' => $_POST['status'],
        ':d' => $_POST['drone_id'] ?: null,
        ':id' => $id
    ]);

    header("Location: order_details.php?id=$id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifica Ordine</title>
    <link rel="stylesheet" href="assets/tech.css">

</head>
<body>

<?php include 'menu.php'; ?>

<div class="auth-container" style="width:450px;">
    <h2>Modifica Ordine #<?= $id ?></h2>

    <form method="POST">

        <input type="text" name="address" value="<?= $order['address'] ?>" placeholder="Indirizzo" required>

        <input type="text" name="package_type" value="<?= $order['package_type'] ?>" placeholder="Tipo pacco" required>

        <input type="number" step="0.01" name="weight" value="<?= $order['weight'] ?>" placeholder="Peso (kg)" required>

        <label style="color:#ccc;">Priorità</label>
        <select name="priority" required style="width:100%; padding:12px; margin-bottom:10px; border-radius:8px; background:#2a2a2a; color:#fff;">
            <option value="bassa" <?= $order['priority']=='bassa'?'selected':'' ?>>Bassa</option>
            <option value="media" <?= $order['priority']=='media'?'selected':'' ?>>Media</option>
            <option value="alta" <?= $order['priority']=='alta'?'selected':'' ?>>Alta</option>
        </select>

        <label style="color:#ccc;">Stato ordine</label>
        <select name="status" required style="width:100%; padding:12px; margin-bottom:10px; border-radius:8px; background:#2a2a2a; color:#fff;">
            <option value="in_attesa" <?= $order['status']=='in_attesa'?'selected':'' ?>>In attesa</option>
            <option value="in_consegna" <?= $order['status']=='in_consegna'?'selected':'' ?>>In consegna</option>
            <option value="completato" <?= $order['status']=='completato'?'selected':'' ?>>Completato</option>
            <option value="annullato" <?= $order['status']=='annullato'?'selected':'' ?>>Annullato</option>
        </select>

        <label style="color:#ccc;">Drone assegnato</label>
        <select name="drone_id" style="width:100%; padding:12px; margin-bottom:10px; border-radius:8px; background:#2a2a2a; color:#fff;">
            <option value="">Nessuno</option>
            <?php foreach ($drones as $d): ?>
                <option value="<?= $d['id'] ?>" <?= $order['drone_id']==$d['id']?'selected':'' ?>>
                    <?= $d['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <textarea name="notes" placeholder="Note" style="width:100%; height:80px; padding:12px; border-radius:8px; background:#2a2a2a; color:#fff;"><?= $order['notes'] ?></textarea>

        <button>Salva modifiche</button>
    </form>

    <div style="text-align:center; margin-top:20px;">
        <a href="order_details.php?id=<?= $id ?>" style="color:#ffcc00; font-weight:bold;">⬅ Torna ai dettagli</a>
    </div>
</div>

</body>
</html>
