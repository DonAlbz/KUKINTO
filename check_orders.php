<?php
require_once 'config.php';

$stmt = $pdo->query("DESCRIBE orders");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($columns);
echo "</pre>";
