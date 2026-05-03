<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, name, status, battery, location FROM drones");
    $drones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($drones);
} catch (Exception $e) {
    echo json_encode([]);
}
