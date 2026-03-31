<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

// Salvataggio nuovo drone
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $status = $_POST['status'];
    $battery = intval($_POST['battery']);
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];

    $stmt = $pdo->prepare("
        INSERT INTO drones (name, status, battery, latitude, longitude)
        VALUES (:name, :status, :battery, :lat, :lng)
    ");

    $stmt->execute([
        ':name' => $name,
        ':status' => $status,
        ':battery' => $battery,
        ':lat' => $lat,
        ':lng' => $lng
    ]);

    header("Location: drones.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nuovo Drone</title>
    <link rel="stylesheet" href="assets/tech.css">


    <style>
        .new-container {
            width: 900px;
            margin: 40px auto;
            background: #1E1E1E;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.4);
        }

        .new-title {
            color: #FFD43B;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .new-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .new-box {
            background: #242424;
            padding: 20px;
            border-radius: 10px;
        }

        .new-box h4 {
            margin: 0 0 10px;
            color: #FFD43B;
            font-size: 18px;
        }

        .new-box input,
        .new-box select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: none;
            background: #1A1A1A;
            color: #EAEAEA;
            font-size: 15px;
        }

        .save-btn {
            margin-top: 30px;
            background: #FFD43B;
            padding: 12px 18px;
            border-radius: 8px;
            color: #000;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #9b7bff;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="new-container">

    <div class="new-title">Aggiungi Nuovo Drone</div>

    <form method="POST">

        <div class="new-grid">

            <div class="new-box">
                <h4>Nome</h4>
                <input type="text" name="name" required>
            </div>

            <div class="new-box">
                <h4>Stato</h4>
                <select name="status">
                    <option value="attivo">Attivo</option>
                    <option value="offline">Offline</option>
                    <option value="occupato">Occupato</option>
                </select>
            </div>

            <div class="new-box">
                <h4>Batteria (%)</h4>
                <input type="number" name="battery" min="0" max="100" required>
            </div>

            <div class="new-box">
                <h4>Latitudine</h4>
                <input type="text" name="latitude" required>
            </div>

            <div class="new-box">
                <h4>Longitudine</h4>
                <input type="text" name="longitude" required>
            </div>

        </div>

        <button type="submit" class="save-btn">Aggiungi Drone</button>
    </form>

    <a href="drones.php" class="back-link">← Torna alla lista droni</a>

</div>

</body>
</html>
