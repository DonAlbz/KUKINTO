<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Drone Delivery Brescia</title>

    <link rel="stylesheet" href="assets/style.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <style>
        /* ============================
           BASE GLOBALE
        ============================ */
        body {
            margin: 0;
            background: #0a0f1f;
            color: #e6f1ff;
            font-family: "Segoe UI", Arial, sans-serif;
            overflow-x: hidden;
        }

        /* ============================
           PATTERN DOTS ANIMATO
        ============================ */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: radial-gradient(#00eaff22 1px, transparent 1px);
            background-size: 22px 22px;
            opacity: 0.25;
            animation: dotsMove 18s linear infinite;
            pointer-events: none;
        }

        @keyframes dotsMove {
            from { transform: translateY(0); }
            to { transform: translateY(-180px); }
        }

        /* ============================
           HERO
        ============================ */
        .hero {
            height: 60vh;
            position: relative;
            padding: 80px;
            display: flex;
            align-items: center;
        }

        .hero-content {
            max-width: 600px;
            z-index: 10;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 10px;
            color: #00eaff;
        }

        .hero p {
            font-size: 20px;
            opacity: 0.85;
        }

        /* ============================
           PULSANTI TECH PREMIUM
        ============================ */
        .btn {
            display: inline-block;
            padding: 12px 26px;
            margin: 10px 12px 0 0;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            letter-spacing: 0.5px;
            font-size: 16px;
            transition: 0.25s ease;
            background: rgba(0, 234, 255, 0.04);
        }

        .btn:hover {
            background: rgba(0, 234, 255, 0.12);
            box-shadow: 0 0 10px #00eaff55;
            transform: translateY(-2px);
        }

        /* ============================
           DRONE MINIMAL NEON (VOLO LIBERO + INERZIA)
        ============================ */
        #drone {
            overflow: visible;
            position: absolute;
            right: 40px;
            top: 120px;
            width: 260px;
            opacity: 0.45;
            pointer-events: none;
            z-index: 50;
        }

        .drone-svg {
    width: 260px;
    filter: drop-shadow(0 0 6px #00eaff55);
    overflow: visible; /* <- QUESTA È LA CHIAVE */
}


        .prop {
            transform-origin: center;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ============================
           SEZIONI SOTTO
        ============================ */
        section {
            width: 90%;
            margin: 40px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
        }

        section h2 {
            color: #00eaff;
            text-align: center;
            margin-bottom: 15px;
        }

        section p {
            text-align: center;
            font-size: 18px;
            opacity: 0.85;
        }

        #map {
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>
<body>

    <!-- ============================
         HERO
    ============================ -->
    <header class="hero">
        <div class="hero-content">
            <h1>Drone Delivery Brescia</h1>
            <p>Consegne rapide tramite droni in tutta Brescia e provincia</p>

            <a href="login_customer.php" class="btn">Accedi come Cliente</a>
            <a href="login.php" class="btn">Accedi come Operatore/Admin</a>
            <br>
            <a href="orders.php" class="btn" style="margin-top:15px;">Gestisci ordini</a>
        </div>
    </header>

    <!-- ============================
         DRONE SVG MINIMAL NEON
    ============================ -->
    <div id="drone">
        <svg class="drone-svg" viewBox="0 0 360 220">
    <rect x="150" y="80" width="60" height="40" rx="8" fill="none" stroke="#00eaff" stroke-width="3"/>
    <line x1="150" y1="100" x2="80" y2="50" stroke="#00eaff" stroke-width="3"/>
    <line x1="210" y1="100" x2="280" y2="50" stroke="#00eaff" stroke-width="3"/>
    <circle class="prop" cx="80" cy="50" r="22" stroke="#00eaff" stroke-width="3" fill="none"/>
    <circle class="prop" cx="280" cy="50" r="22" stroke="#00eaff" stroke-width="3" fill="none"/>
    <rect x="170" y="135" width="20" height="20" rx="4" fill="none" stroke="#00eaff" stroke-width="2"/>
</svg>


    </div>

    <!-- ============================
         SEZIONE INFO
    ============================ -->
    <section>
        <h2>Come funziona</h2>
        <p>
            Inserisci il tuo ordine, scegli indirizzo di ritiro e consegna a Brescia e provincia.
            Un drone disponibile verrà assegnato automaticamente e potrai seguire lo stato della consegna.
        </p>
    </section>

    <!-- ============================
         MAPPA
    ============================ -->
    <section>
        <h2>Mappa Droni in Tempo Reale</h2>
        <div id="map"></div>
    </section>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/app.js"></script>

    <!-- ============================
         ANIMAZIONE GSAP DRONE (VOLO CON INERZIA)
    ============================ -->
    <script>
        gsap.to("#drone", {
            opacity: 1,
            duration: 1.5,
            ease: "power2.out"
        });

        function inertiaFlight() {
            const x = (Math.random() - 0.5) * 300;
            const y = (Math.random() - 0.5) * 200;
            const duration = 3 + Math.random() * 3;

            gsap.to("#drone", {
                x: "+=" + x,
                y: "+=" + y,
                duration: duration,
                ease: "power2.inOut",
                onComplete: inertiaFlight,
                overwrite: "auto"
            });
        }

        inertiaFlight();
    </script>

</body>
</html>
