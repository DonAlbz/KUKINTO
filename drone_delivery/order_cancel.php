<?php
require_once 'auth_operatore.php';
require_once 'config.php';

if (!isset($_SESSION['operator_id'])) {
    header("Location: login_operatore.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: orders_operatore.php");
    exit;
}

$orderId = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT assigned_drone FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: orders_operatore.php");
    exit;
}

if (!empty($order['assigned_drone'])) {
    $pdo->prepare("UPDATE drones SET status='attivo', current_order=NULL WHERE id=?")
        ->execute([$order['assigned_drone']]);
    $pdo->prepare("DELETE FROM drone_tracking WHERE drone_id=?")
        ->execute([$order['assigned_drone']]);
}

$pdo->prepare("UPDATE orders SET status='annullato', updated_at=NOW() WHERE id=?")
    ->execute([$orderId]);

header("Location: orders_operatore.php");
exit;
