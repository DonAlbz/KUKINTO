<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// Recupera droni
$stmt = $pdo->query("SELECT id, name, status, battery, location FROM drones");
$drones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se un drone non ha coordinate → generiamo coordinate simulate
foreach ($drones as &$d) {
    if (!$d['location']) {
        $lat = 45.5416 + (rand(-100, 100) / 10000);
        $lng = 10.2118 + (rand(-100, 100) / 10000);
        $d['location'] = "$lat,$lng";
    }
}

header('Content-Type: application/json');
echo json_encode($drones);
