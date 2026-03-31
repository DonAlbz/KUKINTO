<?php
$current = basename($_SERVER['PHP_SELF']); // pagina attiva

echo '
<nav class="top-nav">

    <!-- LOGO + ICONA DRONE -->
    <div class="nav-left">
        <div class="drone-icon"></div>
        <span class="brand">DroneDelivery</span>
    </div>

    <!-- MENU DESKTOP -->
    <div class="nav-links">
        <a href="dashboard.php" class="'.($current=="dashboard.php"?"active":"").'">Dashboard</a>
        <a href="orders.php" class="'.($current=="orders.php"?"active":"").'">Ordini</a>
        <a href="drones.php" class="'.($current=="drones.php"?"active":"").'">Droni</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- SWITCH TEMA -->
    <div class="theme-switch" onclick="toggleTheme()"></div>

    <!-- HAMBURGER MENU -->
    <div class="hamburger" onclick="toggleMobileMenu()">
        <span></span><span></span><span></span>
    </div>

</nav>

<!-- MENU MOBILE -->
<div class="mobile-menu" id="mobileMenu">
    <a href="dashboard.php" class="'.($current=="dashboard.php"?"active":"").'">Dashboard</a>
    <a href="orders.php" class="'.($current=="orders.php"?"active":"").'">Ordini</a>
    <a href="drones.php" class="'.($current=="drones.php"?"active":"").'">Droni</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<style>

/* ============================
   NAVBAR BASE
============================ */
.top-nav {
    width: 100%;
    background: rgba(255,255,255,0.03);
    border-bottom: 1px solid #00eaff33;
    padding: 14px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    backdrop-filter: blur(6px);
    position: sticky;
    top: 0;
    z-index: 100;
}

/* ============================
   LOGO + ICONA DRONE
============================ */
.nav-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand {
    color: #00eaff;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Icona drone neon */
.drone-icon {
    width: 28px;
    height: 28px;
    border: 2px solid #00eaff;
    border-radius: 50%;
    position: relative;
    box-shadow: 0 0 8px #00eaff;
    animation: droneGlow 2s infinite alternate;
}

.drone-icon::before,
.drone-icon::after {
    content: "";
    position: absolute;
    width: 18px;
    height: 2px;
    background: #00eaff;
    top: 50%;
    transform: translateY(-50%);
}

.drone-icon::before { left: -12px; }
.drone-icon::after { right: -12px; }

@keyframes droneGlow {
    from { box-shadow: 0 0 6px #00eaff; }
    to   { box-shadow: 0 0 14px #00eaff; }
}

/* ============================
   LINK DESKTOP
============================ */
.nav-links {
    display: flex;
    gap: 25px;
}

.nav-links a {
    color: #00eaff;
    text-decoration: none;
    font-weight: 500;
    padding: 6px 10px;
    border-radius: 4px;
    transition: 0.25s;
}

/* Hover neon animato */
.nav-links a:hover {
    background: rgba(0,234,255,0.12);
    box-shadow: 0 0 8px #00eaff55;
}

/* Indicatore pagina attiva */
.nav-links a.active {
    background: rgba(0,234,255,0.18);
    box-shadow: 0 0 10px #00eaff88;
}

/* Logout rosso */
.logout {
    color: #ff4444 !important;
    border: 1px solid #ff4444;
}

.logout:hover {
    background: rgba(255,68,68,0.12);
    box-shadow: 0 0 8px #ff444455;
}

/* ============================
   TEMA LIGHT/DARK SWITCH
============================ */
.theme-switch {
    width: 26px;
    height: 26px;
    border: 2px solid #00eaff;
    border-radius: 50%;
    cursor: pointer;
    transition: 0.3s;
}

.theme-switch:hover {
    box-shadow: 0 0 10px #00eaff;
}

/* ============================
   HAMBURGER MENU (MOBILE)
============================ */
.hamburger {
    display: none;
    flex-direction: column;
    gap: 4px;
    cursor: pointer;
}

.hamburger span {
    width: 26px;
    height: 3px;
    background: #00eaff;
    border-radius: 3px;
}

/* ============================
   MOBILE MENU
============================ */
.mobile-menu {
    display: none;
    flex-direction: column;
    background: rgba(0,0,0,0.7);
    border-bottom: 1px solid #00eaff33;
    padding: 15px;
}

.mobile-menu a {
    padding: 10px 0;
    color: #00eaff;
    text-decoration: none;
    font-weight: 500;
}

.mobile-menu a.active {
    color: #00ffff;
    text-shadow: 0 0 6px #00eaff;
}

/* ============================
   RESPONSIVE
============================ */
@media (max-width: 780px) {
    .nav-links {
        display: none;
    }
    .hamburger {
        display: flex;
    }
}
</style>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById("mobileMenu");
    menu.style.display = menu.style.display === "flex" ? "none" : "flex";
}

function toggleTheme() {
    document.body.classList.toggle("light-theme");
}
</script>

<style>
/* Tema chiaro */
.light-theme {
    background: #f2faff;
    color: #003344;
}

.light-theme .top-nav {
    background: rgba(255,255,255,0.7);
    border-bottom: 1px solid #00aacc33;
}

.light-theme a {
    color: #0088aa !important;
}

.light-theme .logout {
    color: #cc2222 !important;
    border-color: #cc2222;
}
</style>
';
?>
