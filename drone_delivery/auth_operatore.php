<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentFile = basename($_SERVER['PHP_SELF']);

/* Se non è loggato → login */
if (!isset($_SESSION['operator_id'])) {
    if ($currentFile !== 'login_operatore.php') {
        header("Location: login_operatore.php");
        exit;
    }
}

/* Se è già loggato e si trova nel login → dashboard */
if (isset($_SESSION['operator_id']) && $currentFile === 'login_operatore.php') {
    header("Location: operatore_dashboard.php");
    exit;
}
?>
