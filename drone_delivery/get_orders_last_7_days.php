<?php
require_once 'config.php';

header('Content-Type: application/json');

// Ultimi 7 giorni
$labels = [];
$values = [];

for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d/m', strtotime($day));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = ?");
    $stmt->execute([$day]);
    $values[] = (int)$stmt->fetchColumn();
}

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
