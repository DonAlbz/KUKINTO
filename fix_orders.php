<?php
require_once 'config.php';

$queries = [
    "ALTER TABLE orders ADD COLUMN customer_id INT AFTER id",
    "ALTER TABLE orders ADD COLUMN pickup_address VARCHAR(255) AFTER customer_id",
    "ALTER TABLE orders ADD COLUMN delivery_address VARCHAR(255) AFTER pickup_address",
    "ALTER TABLE orders ADD COLUMN notes TEXT AFTER delivery_address",
    "ALTER TABLE orders ADD COLUMN priority VARCHAR(20) DEFAULT 'media' AFTER notes",
    "ALTER TABLE orders ADD COLUMN status VARCHAR(50) DEFAULT 'in_attesa' AFTER priority",
    "ALTER TABLE orders ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER status",
    "ALTER TABLE orders ADD COLUMN updated_at DATETIME NULL AFTER created_at"
];

foreach ($queries as $q) {
    try {
        $pdo->exec($q);
        echo "OK: $q<br>";
    } catch (Exception $e) {
        echo "SKIPPED (probabilmente già esiste): $q<br>";
    }
}
