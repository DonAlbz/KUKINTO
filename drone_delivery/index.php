<?php ?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>DroneDelivery Brescia</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body {
    background: #0a0f1f;
    color: #e6f1ff;
    font-family: "Segoe UI", Arial, sans-serif;
    overflow-x: hidden;
}
a { text-decoration: none; }

/* PATTERN ANIMATO */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background-image: radial-gradient(#00eaff18 1px, transparent 1px);
    background-size: 24px 24px;
    animation: dotsMove 20s linear infinite;
    pointer-events: none;
    z-index: 0;
}
@keyframes dotsMove {
    from { transform: translateY(0); }
    to   { transform: translateY(-192px); }
}

/* NAVBAR */
nav {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 40px;
    background: rgba(10,15,31,0.85);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0,234,255,0.1);
}
.nav-logo {
    display: flex;
    align-items: center;
    gap: 10px;
}
.nav-logo-icon {
    width: 28px; height: 28px;
    background: url('assets/drone_icon.svg') center/contain no-repeat;
    filter: drop-shadow(0 0 6px #00eaff);
}
.nav-logo-text {
    color: #00eaff;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 0.4px;
}
.nav-links {
    display: flex;
    align-items: center;
    gap: 12px;
}
.btn-nav {
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid;
    transition: 0.2s;
}
.btn-cliente {
    color: #00eaff;
    border-color: rgba(0,234,255,0.3);
    background: rgba(0,234,255,0.06);
}
.btn-cliente:hover { background: rgba(0,234,255,0.14); }
.btn-operatore {
    color: #a78bfa;
    border-color: rgba(167,139,250,0.3);
    background: rgba(167,139,250,0.06);
}
.btn-operatore:hover { background: rgba(167,139,250,0.14); }

/* HERO */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 120px 40px 80px;
    z-index: 1;
    overflow: hidden;
}
.hero-glow {
    position: absolute;
    width: 600px; height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0,234,255,0.08) 0%, transparent 70%);
    pointer-events: none;
}
.hero-content { position: relative; z-index: 2; }
.hero-tag {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 999px;
    border: 1px solid rgba(0,234,255,0.25);
    background: rgba(0,234,255,0.06);
    color: #00eaff;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 20px;
}
.hero h1 {
    font-size: clamp(42px, 6vw, 72px);
    color: #00eaff;
    text-shadow: 0 0 40px rgba(0,234,255,0.3);
    margin-bottom: 16px;
    line-height: 1.1;
}
.hero p {
    font-size: clamp(16px, 2vw, 22px);
    color: #8aaec8;
    margin-bottom: 36px;
    max-width: 520px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}
.hero-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.btn-hero {
    padding: 13px 28px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    border: 1px solid;
    transition: 0.2s;
}
.btn-hero-primary {
    color: #0a0f1f;
    background: #00eaff;
    border-color: #00eaff;
}
.btn-hero-primary:hover { box-shadow: 0 0 24px rgba(0,234,255,0.5); transform: translateY(-2px); }
.btn-hero-secondary {
    color: #00eaff;
    background: rgba(0,234,255,0.06);
    border-color: rgba(0,234,255,0.3);
}
.btn-hero-secondary:hover { background: rgba(0,234,255,0.12); transform: translateY(-2px); }

a {
    color: #00eaff;
    background: #0a0f1f;
    border-color: #00eaff;
}

/* DRONE */
#drone {
    position: absolute;
    right: 8%;
    top: 20%;
    width: 280px;
    opacity: 0;
    pointer-events: none;
    z-index: 3;
}
.drone-svg { width: 280px; filter: drop-shadow(0 0 8px #00eaff44); overflow: visible; }
.prop { transform-origin: center; animation: spin 0.5s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* STATS BAR */
.stats-bar {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    gap: 0;
    margin: 0 40px 80px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(0,234,255,0.1);
    border-radius: 16px;
    overflow: hidden;
}
.stat-item {
    flex: 1;
    padding: 28px 20px;
    text-align: center;
    border-right: 1px solid rgba(0,234,255,0.08);
}
.stat-item:last-child { border-right: none; }
.stat-num { font-size: 36px; font-weight: 700; color: #00eaff; margin-bottom: 4px; }
.stat-desc { font-size: 13px; color: #5a7a99; }

/* FEATURES */
.features {
    padding: 0 40px 80px;
    position: relative;
    z-index: 1;
}
.section-label {
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #00eaff;
    margin-bottom: 12px;
}
.section-title {
    text-align: center;
    font-size: clamp(28px, 4vw, 40px);
    color: #e6f1ff;
    margin-bottom: 48px;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    max-width: 1100px;
    margin: 0 auto;
}
.feature-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(0,234,255,0.08);
    border-radius: 14px;
    padding: 28px 24px;
    transition: 0.2s;
}
.feature-card:hover {
    background: rgba(0,234,255,0.04);
    border-color: rgba(0,234,255,0.2);
    transform: translateY(-4px);
}
.feature-icon { font-size: 28px; margin-bottom: 14px; }
.feature-title { font-size: 17px; font-weight: 600; color: #e6f1ff; margin-bottom: 8px; }
.feature-desc { font-size: 14px; color: #5a7a99; line-height: 1.6; }

/* BANNER */
.banner {
    display: flex;
    align-items: center;
    gap: 60px;
    max-width: 1100px;
    margin: 0 auto 100px;
    padding: 0 40px;
    opacity: 0;
    transform: translateY(40px);
    transition: 1s ease;
    position: relative;
    z-index: 1;
}
.banner.visible { opacity: 1; transform: translateY(0); }
.banner.reverse { flex-direction: row-reverse; }
.banner img {
    width: 45%;
    border-radius: 16px;
    border: 1px solid rgba(0,234,255,0.15);
    box-shadow: 0 0 30px rgba(0,234,255,0.1);
}
.banner-text { flex: 1; }
.banner-text h2 { font-size: clamp(24px, 3vw, 36px); color: #00eaff; margin-bottom: 14px; }
.banner-text p { font-size: 16px; color: #8aaec8; line-height: 1.7; }

/* CTA */
.cta {
    text-align: center;
    padding: 80px 40px;
    position: relative;
    z-index: 1;
    border-top: 1px solid rgba(0,234,255,0.08);
}
.cta h2 { font-size: clamp(28px, 4vw, 42px); color: #e6f1ff; margin-bottom: 10px; }
.cta p { color: #5a7a99; font-size: 16px; margin-bottom: 32px; }

/* RESPONSIVE */
@media (max-width: 900px) {
    .banner, .banner.reverse { flex-direction: column; }
    .banner img { width: 100%; }
    .features-grid { grid-template-columns: 1fr; }
    .stats-bar { flex-direction: column; }
    #drone { display: none; }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="nav-logo">
        <div class="nav-logo-icon"></div>
        <span class="nav-logo-text">DroneDelivery</span>
    </div>
    <div class="nav-links">
        <a href="login_customer.php" class="btn-nav btn-cliente">Area Cliente</a>
        <a href="login_operatore.php" class="btn-nav btn-operatore">Area Operatore</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-glow"></div>

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

    <div class="hero-content">
        <div class="hero-tag">🚁 Brescia · Consegne autonome</div>
        <h1>La consegna<br>del futuro, oggi.</h1>
        <p>Ordina, traccia e ricevi in pochi minuti grazie ai nostri droni autonomi con navigazione GPS di precisione.</p>
        <div class="hero-btns">
            <a href="login_customer.php" class="btn-hero btn-hero-primary">Inizia ora</a>
            <a href="login_operatore.php" class="btn-hero btn-hero-secondary">Pannello operatore</a>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-num">< 15'</div>
        <div class="stat-desc">Tempo medio di consegna</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">25 km</div>
        <div class="stat-desc">Raggio operativo</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">10 kg</div>
        <div class="stat-desc">Capacità di carico massima</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">24/7</div>
        <div class="stat-desc">Servizio attivo</div>
    </div>
</div>

<!-- FEATURES GRID -->
<div class="features">
    <div class="section-label">Perché sceglierci</div>
    <h2 class="section-title">Tutto quello che ti serve</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📡</div>
            <div class="feature-title">Tracking in tempo reale</div>
            <div class="feature-desc">Segui il tuo ordine sulla mappa con precisione al metro, grazie al sistema HUD integrato.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <div class="feature-title">Consegne ultra-rapide</div>
            <div class="feature-desc">I nostri droni autonomi consegnano in pochi minuti con percorsi ottimizzati in tempo reale.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔔</div>
            <div class="feature-title">Notifiche intelligenti</div>
            <div class="feature-desc">Aggiornamenti automatici su ogni fase della consegna, dal ritiro all'arrivo.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📍</div>
            <div class="feature-title">Zero errori negli indirizzi</div>
            <div class="feature-desc">Geocoding avanzato con validazione automatica di ogni indirizzo su Brescia e dintorni.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🛡️</div>
            <div class="feature-title">Sicurezza certificata</div>
            <div class="feature-desc">Ogni volo è monitorato e conforme alle normative ENAC per droni commerciali.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📦</div>
            <div class="feature-title">Fino a 10 kg</div>
            <div class="feature-desc">Trasporto di pacchi, documenti e merci fino a 10 kg con stabilità e precisione assoluta.</div>
        </div>
    </div>
</div>

<!-- BANNER 1 -->
<div class="banner">
    <img src="assets/radar.png" alt="Radar tracking">
    <div class="banner-text">
        <h2>Tracking in tempo reale</h2>
        <p>Segui il tuo ordine sulla mappa con precisione al metro, grazie al nostro sistema HUD neon integrato direttamente nell'app.</p>
            <a href="tracking_tempo_reale.php" class="info-link">Maggiori informazioni ></a>
    </div>
</div>

<!-- BANNER 2 -->
<div class="banner reverse">
    <img src="assets/drone.png" alt="Drone">
    <div class="banner-text">
        <h2>Consegne ultra-rapide</h2>
        <p>I nostri droni autonomi consegnano in pochi minuti, con percorsi ottimizzati e tecnologia di navigazione GPS centimetrica.</p>
            <a href="consegne_ultra_rapide.php" class="info-link">Maggiori informazioni ></a>   
    </div>
</div>

<!-- BANNER 3 -->
<div class="banner">
    <img src="assets/mappa.png" alt="Mappa">
    <div class="banner-text">
        <h2>Zero errori negli indirizzi</h2>
        <p>Grazie al geocoding avanzato, ogni indirizzo viene verificato e corretto automaticamente prima della consegna.</p>
            <a href="indirizzi_perfetti.php" class="info-link">Maggiori informazioni ></a>
    </div>
</div>

<!-- CTA FINALE -->
<div class="cta">
    <h2>Pronto a iniziare?</h2>
    <p>Registrati gratuitamente e ricevi il tuo primo ordine in meno di 15 minuti.</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
        <a href="signup_customer.php" class="btn-hero btn-hero-primary">Crea un account</a>
        <a href="login_customer.php" class="btn-hero btn-hero-secondary">Accedi</a>
    </div>
</div>

<script>
/* DRONE */
gsap.to("#drone", { opacity: 0.6, duration: 1.5, ease: "power2.out" });
function inertiaFlight() {
    gsap.to("#drone", {
        x: "+=" + (Math.random() - 0.5) * 200,
        y: "+=" + (Math.random() - 0.5) * 120,
        duration: 3 + Math.random() * 3,
        ease: "power2.inOut",
        onComplete: inertiaFlight,
        overwrite: "auto"
    });
}
inertiaFlight();

/* BANNER SCROLL */
const banners = document.querySelectorAll('.banner');
function checkScroll() {
    banners.forEach(b => {
        if (b.getBoundingClientRect().top < window.innerHeight - 100) {
            b.classList.add('visible');
        }
    });
}
window.addEventListener('scroll', checkScroll);
checkScroll();
</script>

</body>
</html>

