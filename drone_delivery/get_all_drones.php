<?php
require_once 'config.php';
header('Content-Type: application/json');

$stmt = $pdo->query("
    SELECT 
        id,
        name,
        status,
        battery,
        latitude,
        longitude,
        current_order,
        (battery < 20) AS low_battery,
        (status = 'offline') AS is_offline
    FROM drones
");

$drones = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($drones);
