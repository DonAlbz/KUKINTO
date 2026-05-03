<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_customer.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav style="
    background:#0a0f1f;
    padding:15px 30px;
    display:flex;
    align-items:center;
    gap:30px;
    border-bottom:1px solid rgba(0,234,255,0.12);
">
    <a href="index.php" style="display:flex;align-items:center;text-decoration:none;gap:10px;">
        <div style="
            width:26px;
            height:26px;
            background:url('assets/drone_icon.svg') center/contain no-repeat;
            filter:drop-shadow(0 0 6px #00eaff);
        "></div>
        <span style="color:#00eaff;font-size:20px;font-weight:600;letter-spacing:0.5px;">DroneDelivery · Cliente</span>
    </a>

    <a href="customer_dashboard.php" style="
        color:<?= $currentPage === 'customer_dashboard.php' ? '#00eaff' : '#e6f1ff' ?>;
        text-shadow:<?= $currentPage === 'customer_dashboard.php' ? '0 0 8px #00eaff' : 'none' ?>;
        text-decoration:none;">
        Dashboard
    </a>

    <a href="orders_customer.php" style="
        color:<?= $currentPage === 'orders_customer.php' ? '#00eaff' : '#e6f1ff' ?>;
        text-shadow:<?= $currentPage === 'orders_customer.php' ? '0 0 8px #00eaff' : 'none' ?>;
        text-decoration:none;">
        I miei ordini
    </a>

    <a href="create_order_customer.php" style="
        color:<?= $currentPage === 'create_order_customer.php' ? '#00eaff' : '#e6f1ff' ?>;
        text-shadow:<?= $currentPage === 'create_order_customer.php' ? '0 0 8px #00eaff' : 'none' ?>;
        text-decoration:none;">
        Nuovo ordine
    </a>

    <a href="customer_logout.php" style="color:#ff4d4d;text-decoration:none;margin-left:auto;">Logout</a>
</nav>