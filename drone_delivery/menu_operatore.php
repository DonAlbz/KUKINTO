<?php
require_once "auth_operatore.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav style="
    background:#0a0f1f;
    padding:15px 30px;
    display:flex;
    align-items:center;
    gap:30px;
    border-bottom:1px solid #00eaff33;
">

    <!-- LOGO CLICCABILE (TORNA ALLA HOME) -->
    <a href="index.php" style="display:flex;align-items:center;text-decoration:none;gap:10px;">
        <div style="
            width:26px;
            height:26px;
            background:url('assets/drone_icon.svg') center/contain no-repeat;
            filter: drop-shadow(0 0 6px #00eaff);
        "></div>

        <span style="
            color:#00eaff;
            font-size:20px;
            font-weight:600;
            letter-spacing:0.5px;
        ">DroneDelivery · Operatore</span>
    </a>

    <!-- LINK MENU -->
    <a href="operatore_dashboard.php"
       style="color:<?= $currentPage === 'operatore_dashboard.php' ? '#00eaff' : '#e6f1ff' ?>;
              text-shadow:<?= $currentPage === 'operatore_dashboard.php' ? '0 0 8px #00eaff' : 'none' ?>;
              text-decoration:none;">
        Dashboard
    </a>

    <a href="orders_operatore.php"
       style="color:<?= $currentPage === 'orders_operatore.php' ? '#00eaff' : '#e6f1ff' ?>;
              text-shadow:<?= $currentPage === 'orders_operatore.php' ? '0 0 8px #00eaff' : 'none' ?>;
              text-decoration:none;">
        Ordini
    </a>

    <a href="drones.php"
       style="color:<?= $currentPage === 'drones.php' ? '#00eaff' : '#e6f1ff' ?>;
              text-shadow:<?= $currentPage === 'drones.php' ? '0 0 8px #00eaff' : 'none' ?>;
              text-decoration:none;">
        Droni
    </a>

    <a href="logout.php" style="color:#ff4d4d;text-decoration:none;margin-left:auto;">Logout</a>
</nav>

