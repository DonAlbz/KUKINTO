<?php
require_once 'config.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
    exit;
}

$user_id = $_POST['user_id'] ?? null;
$pickup = $_POST['pickup_address_id'] ?? null;
$delivery = $_POST['delivery_address_id'] ?? null;
$drone_id = $_POST['drone_id'] ?? null;

if (!$user_id || !$pickup || !$delivery) {
    http_response_code(400);
    echo json_encode(['error' => 'Dati mancanti']);
    exit;
}

try {
    $sql = "INSERT INTO orders (user_id, pickup_address_id, delivery_address_id, drone_id)
            VALUES (:user_id, :pickup, :delivery, :drone_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':pickup' => $pickup,
        ':delivery' => $delivery,
        ':drone_id' => $drone_id ?: null
    ]);

    echo json_encode(['success' => true, 'order_id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore server']);
}
