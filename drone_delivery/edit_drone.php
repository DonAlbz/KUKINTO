<?php
require_once 'auth_operatore.php';
require_once 'config.php';

if (!isset($_SESSION['operator_id'])) {
    header("Location: login_operatore.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: drones.php");
    exit;
}

$droneId = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM drones WHERE id = :id");
$stmt->execute([':id' => $droneId]);
$drone = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$drone) {
    header("Location: drones.php");
    exit;
}

$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $status  = $_POST['status'];
    $battery = intval($_POST['battery']);
    $lat     = trim($_POST['latitude']);
    $lng     = trim($_POST['longitude']);

    $pdo->prepare("
        UPDATE drones SET name=:name, status=:status, battery=:battery, latitude=:lat, longitude=:lng
        WHERE id=:id
    ")->execute([':name'=>$name, ':status'=>$status, ':battery'=>$battery, ':lat'=>$lat, ':lng'=>$lng, ':id'=>$droneId]);

    $success = true;
    $message = "Drone aggiornato con successo.";
    $drone = array_merge($drone, ['name'=>$name,'status'=>$status,'battery'=>$battery,'latitude'=>$lat,'longitude'=>$lng]);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Modifica Drone #<?= $droneId ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
* { box-sizing:border-box; }
body { margin:0; background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }
.page { max-width:600px; margin:0 auto; padding:28px 28px 60px; }
.page-header { display:flex; align-items:center; gap:14px; margin-bottom:28px; }
.back-btn { display:inline-flex; align-items:center; gap:6px; color:#5a7a99; font-size:13px; padding:6px 12px; border:1px solid rgba(90,122,153,0.3); border-radius:8px; transition:0.15s; }
.back-btn:hover { color:#00eaff; border-color:rgba(0,234,255,0.3); }
.page-title { color:#00eaff; font-size:20px; font-weight:600; margin:0; }
.card { background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.1); border-radius:14px; padding:24px; }
.card-title { color:#00eaff; font-size:13px; font-weight:600; letter-spacing:0.7px; text-transform:uppercase; margin:0 0 20px; padding-bottom:10px; border-bottom:1px solid rgba(0,234,255,0.08); }
.field { margin-bottom:16px; }
.field label { display:block; font-size:12px; color:#5a7a99; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase; }
.field input, .field select {
    width:100%; background:#0d1628; border:1px solid rgba(0,234,255,0.2);
    border-radius:10px; padding:11px 14px; color:#e6f1ff; font-size:14px; outline:none; transition:0.2s;
}
.field input:focus, .field select:focus { border-color:rgba(0,234,255,0.5); }
.field select option { background:#0d1628; color:#e6f1ff; }
.grid2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.btn-save {
    width:100%; padding:12px; background:rgba(0,234,255,0.08); border:1px solid rgba(0,234,255,0.35);
    border-radius:10px; color:#00eaff; font-size:14px; font-weight:600; cursor:pointer; transition:0.2s; margin-top:8px;
}
.btn-save:hover { background:rgba(0,234,255,0.16); box-shadow:0 0 20px rgba(0,234,255,0.15); }
.success { margin-top:14px; padding:10px 14px; background:rgba(0,230,118,0.08); border:1px solid rgba(0,230,118,0.2); border-radius:8px; color:#00e676; font-size:13px; text-align:center; }
</style>
</head>
<body>
<?php include 'menu_operatore.php'; ?>

<div class="page">
    <div class="page-header">
        <a href="drones.php" class="back-btn">&#8592; Droni</a>
        <h1 class="page-title">Modifica Drone #<?= $droneId ?></h1>
    </div>

    <div class="card">
        <p class="card-title">Informazioni drone</p>
        <form method="POST">
            <div class="field">
                <label>Nome</label>
                <input type="text" name="name" value="<?= htmlspecialchars($drone['name']) ?>" required>
            </div>
            <div class="grid2">
                <div class="field">
                    <label>Stato</label>
                    <select name="status">
                        <option value="attivo"   <?= $drone['status']==='attivo'   ? 'selected':'' ?>>Attivo</option>
                        <option value="occupato" <?= $drone['status']==='occupato' ? 'selected':'' ?>>Occupato</option>
                        <option value="offline"  <?= $drone['status']==='offline'  ? 'selected':'' ?>>Offline</option>
                    </select>
                </div>
                <div class="field">
                    <label>Batteria (%)</label>
                    <input type="number" name="battery" min="0" max="100" value="<?= htmlspecialchars($drone['battery']) ?>" required>
                </div>
            </div>
            <div class="grid2">
                <div class="field">
                    <label>Latitudine</label>
                    <input type="text" name="latitude" value="<?= htmlspecialchars($drone['latitude'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label>Longitudine</label>
                    <input type="text" name="longitude" value="<?= htmlspecialchars($drone['longitude'] ?? '') ?>" required>
                </div>
            </div>
            <button type="submit" class="btn-save">Salva modifiche</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>