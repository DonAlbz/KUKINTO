<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

if (!isset($_GET['id'])) {
    die("ID drone mancante.");
}

$droneId = intval($_GET['id']);

// Recupero dati drone
$stmt = $pdo->prepare("SELECT * FROM drones WHERE id = :id");
$stmt->execute([':id' => $droneId]);
$drone = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$drone) {
    die("Drone non trovato.");
}

// Salvataggio modifiche
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $status = $_POST['status'];
    $battery = intval($_POST['battery']);
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];

    $update = $pdo->prepare("
        UPDATE drones 
        SET name = :name, status = :status, battery = :battery, latitude = :lat, longitude = :lng
        WHERE id = :id
    ");

    $update->execute([
        ':name' => $name,
        ':status' => $status,
        ':battery' => $battery,
        ':lat' => $lat,
        ':lng' => $lng,
        ':id' => $droneId
    ]);

    header("Location: drones.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Drone</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
    * {
        box-sizing: border-box;
    }

    .edit-container {
        width: 900px;
        margin: 40px auto;
        background: rgba(255, 255, 255, 0.03);
        padding: 30px;
        border-radius: 12px;
        border: 1px solid #00eaff33;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #00eaff;
    }

    .edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .edit-box {
        background: rgba(255, 255, 255, 0.03);
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #00eaff22;
    }

    .edit-box h4 {
        margin: 0 0 10px;
        color: #00eaff;
        font-size: 18px;
    }

    .edit-box input,
    .edit-box select {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #00eaff33;
        background: rgba(255, 255, 255, 0.05);
        color: #e6f1ff;
        font-size: 15px;
    }

    .save-btn {
        margin-top: 30px;
        width: 100%;
        padding: 12px;
        border: 1px solid #00eaff;
        background: rgba(0, 234, 255, 0.04);
        color: #00eaff;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #00eaff;
        text-decoration: none;
        font-weight: 500;
    }
</style>


</head>
<body>

<?php include 'menu.php'; ?>

<div class="edit-container">

    <h1>Modifica Drone #<?= $drone['id'] ?></h1>

    <form method="POST">

        <div class="edit-grid">

            <div class="edit-box">
                <h4>Nome</h4>
                <input type="text" name="name" value="<?= htmlspecialchars($drone['name'] ?? '') ?>" required>
            </div>

            <div class="edit-box">
                <h4>Stato</h4>
                <select name="status">
                    <option value="attivo" <?= $drone['status']=='attivo'?'selected':'' ?>>Attivo</option>
                    <option value="offline" <?= $drone['status']=='offline'?'selected':'' ?>>Offline</option>
                    <option value="occupato" <?= $drone['status']=='occupato'?'selected':'' ?>>Occupato</option>
                </select>
            </div>

            <div class="edit-box">
                <h4>Batteria (%)</h4>
                <input type="number" name="battery" min="0" max="100" value="<?= htmlspecialchars($drone['battery'] ?? '') ?>" required>
            </div>

            <div class="edit-box">
                <h4>Latitudine</h4>
                <input type="text" name="latitude" value="<?= htmlspecialchars($drone['latitude'] ?? '') ?>" required>
            </div>

            <div class="edit-box">
                <h4>Longitudine</h4>
                <input type="text" name="longitude" value="<?= htmlspecialchars($drone['longitude'] ?? '') ?>" required>
            </div>

        </div>

        <button type="submit" class="save-btn">Salva Modifiche</button>
    </form>

    <a href="drones.php" class="back-link">← Torna alla lista droni</a>

</div>

</body>
</html>
