<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

function requireRole($role) {
    if ($_SESSION['role'] !== $role) {
        header("Location: dashboard.php");
        exit;
    }
}
?>
