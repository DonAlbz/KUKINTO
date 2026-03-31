<?php
require_once 'config.php';

$queries = [
    "ALTER TABLE orders DROP COLUMN customer",
    "ALTER TABLE orders DROP COLUMN pickup",
    "ALTER TABLE orders DROP COLUMN delivery",
    "ALTER TABLE orders DROP COLUMN drone_code"
];

foreach ($queries as $q) {
    try {
        $pdo->exec($q);
        echo "OK: $q<br>";
    } catch (Exception $e) {
        echo "SKIPPED: $q<br>";
    }
}
