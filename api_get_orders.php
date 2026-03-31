<?php
require_once 'config.php';

header('Content-Type: application/json');

$stmt = $pdo->query("
    SELECT o.id, u.name AS customer, o.status, o.created_at, d.code AS drone_code
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN drones d ON o.drone_id = d.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($orders);
