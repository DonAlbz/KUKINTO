<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

if (!isset($_GET['id'])) {
    die("ID ordine mancante.");
}

$id = $_GET['id'];
$userRole = $_SESSION['role'];
$userId   = $_SESSION['user_id'];

// Recupera l'ordine
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Ordine non trovato.");
}

// Controllo permessi
if ($userRole === 'cliente' && $order['user_id'] != $userId) {
    die("Non hai i permessi per eliminare questo ordine.");
}

// Elimina ordine
$stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
$stmt->execute([':id' => $id]);

header("Location: orders.php");
exit;
