<?php
$host = 'localhost';
$db   = 'drone_delivery';
$user = 'root';
$pass = ''; // di default su XAMPP è vuota

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}
