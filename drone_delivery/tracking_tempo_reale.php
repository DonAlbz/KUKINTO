<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Tracking in tempo reale — DroneDelivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { background:#0a0f1f; color:#e6f1ff; font-family:"Segoe UI",Arial,sans-serif; overflow-x:hidden; }
a { text-decoration:none; }
body::before { content:""; position:fixed; inset:0; background-image:radial-gradient(#00eaff18 1px,transparent 1px); background-size:24px 24px; animation:dotsMove 20s linear infinite; pointer-events:none; z-index:0; }
@keyframes dotsMove { from{transform:translateY(0)} to{transform:translateY(-192px)} }
nav { position:fixed; top:0; left:0; right:0; z-index:100; display:flex; align-items:center; justify-content:space-between; padding:14px 40px; background:rgba(10,15,31,0.85); backdrop-filter:blur(10px); border-bottom:1px solid rgba(0,234,255,0.1); }
.nav-logo { display:flex; align-items:center; gap:10px; }
.nav-logo-icon { width:28px; height:28px; background:url('assets/drone_icon.svg') center/contain no-repeat; filter:drop-shadow(0 0 6px #00eaff); }
.nav-logo-text { color:#00eaff; font-size:18px; font-weight:600; }
.nav-links { display:flex; gap:12px; }
.btn-nav { padding:8px 20px; border-radius:8px; font-size:13px; font-weight:500; border:1px solid; transition:0.2s; }
.btn-cliente { color:#00eaff; border-color:rgba(0,234,255,0.3); background:rgba(0,234,255,0.06); }
.btn-cliente:hover { background:rgba(0,234,255,0.14); }
.btn-back { color:#5a7a99; border-color:rgba(90,122,153,0.3); background:transparent; }
.btn-back:hover { color:#e6f1ff; }
.page { position:relative; z-index:1; padding:120px 40px 80px; max-width:1000px; margin:0 auto; }
.hero-tag { display:inline-block; padding:5px 14px; border-radius:999px; border:1px solid rgba(0,234,255,0.25); background:rgba(0,234,255,0.06); color:#00eaff; font-size:12px; font-weight:600; letter-spacing:1px; text-transform:uppercase; margin-bottom:20px; }
.page-title { font-size:clamp(36px,5vw,60px); color:#00eaff; text-shadow:0 0 40px rgba(0,234,255,0.3); margin-bottom:16px; line-height:1.1; }
.page-subtitle { font-size:18px; color:#8aaec8; line-height:1.7; margin-bottom:60px; max-width:600px; }
.section { display:flex; align-items:center; gap:60px; margin-bottom:80px; opacity:0; transform:translateY(40px); transition:1s ease; }
.section.visible { opacity:1; transform:translateY(0); }
.section.reverse { flex-direction:row-reverse; }
.section img { width:45%; border-radius:16px; border:1px solid rgba(0,234,255,0.15); box-shadow:0 0 30px rgba(0,234,255,0.1); }
.section-text h2 { font-size:28px; color:#e6f1ff; margin-bottom:14px; }
.section-text p { font-size:15px; color:#8aaec8; line-height:1.8; margin-bottom:12px; }
.feature-list { list-style:none; margin-top:16px; }
.feature-list li { padding:8px 0; font-size:14px; color:#cfe8ff; border-bottom:1px solid rgba(0,234,255,0.06); display:flex; align-items:center; gap:10px; }
.feature-list li::before { content:'→'; color:#00eaff; font-weight:700; }
.card-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:80px; }
.info-card { background:rgba(255,255,255,0.02); border:1px solid rgba(0,234,255,0.08); border-radius:14px; padding:24px; transition:0.2s; }
.info-card:hover { background:rgba(0,234,255,0.04); border-color:rgba(0,234,255,0.2); transform:translateY(-4px); }
.info-icon { font-size:26px; margin-bottom:12px; }
.info-title { font-size:16px; font-weight:600; color:#e6f1ff; margin-bottom:8px; }
.info-desc { font-size:13px; color:#5a7a99; line-height:1.6; }
.cta { text-align:center; padding:60px 40px; border-top:1px solid rgba(0,234,255,0.08); }
.cta h2 { font-size:32px; color:#e6f1ff; margin-bottom:10px; }
.cta p { color:#5a7a99; margin-bottom:28px; font-size:15px; }
.btn-hero { display:inline-block; padding:12px 28px; border-radius:10px; font-size:15px; font-weight:600; border:1px solid; transition:0.2s; }
.btn-primary { color:#0a0f1f; background:#00eaff; border-color:#00eaff; }
.btn-primary:hover { box-shadow:0 0 24px rgba(0,234,255,0.5); transform:translateY(-2px); }
@media(max-width:768px) { .section,.section.reverse{flex-direction:column} .section img{width:100%} .card-grid{grid-template-columns:1fr} }
</style>
</head>
<body>
<nav>
    <a href="index.php" class="nav-logo">
        <div class="nav-logo-icon"></div>
        <span class="nav-logo-text">DroneDelivery</span>
    </a>
    <div class="nav-links">
        <a href="index.php" class="btn-nav btn-back">← Home</a>
        <a href="login_customer.php" class="btn-nav btn-cliente">Area Cliente</a>
    </div>
</nav>

<div class="page">
    <div class="hero-tag">📡 Live tracking</div>
    <h1 class="page-title">Tracking<br>in tempo reale</h1>
    <p class="page-subtitle">Sapere dove si trova il tuo pacco in ogni momento non è più un lusso. Con DroneDelivery, ogni consegna è visibile sulla mappa in tempo reale.</p>

    <div class="card-grid">
        <div class="info-card">
            <div class="info-icon">📍</div>
            <div class="info-title">Posizione al metro</div>
            <div class="info-desc">Il drone trasmette la sua posizione GPS ogni 2 secondi. La mappa si aggiorna in automatico.</div>
        </div>
        <div class="info-card">
            <div class="info-icon">⏱️</div>
            <div class="info-title">ETA preciso</div>
            <div class="info-desc">Tempo di arrivo stimato in tempo reale, aggiornato dinamicamente in base alla velocità del drone.</div>
        </div>
        <div class="info-card">
            <div class="info-icon">🔔</div>
            <div class="info-title">Notifiche automatiche</div>
            <div class="info-desc">Ricevi un aggiornamento quando il drone parte, è in avvicinamento e quando arriva a destinazione.</div>
        </div>
    </div>

    <div class="section">
        <img src="assets/radar.png" alt="Radar tracking">
        <div class="section-text">
            <h2>HUD neon integrato</h2>
            <p>Il pannello di controllo mostra in tempo reale velocità, batteria, distanza rimasta e tempo stimato di arrivo del tuo drone.</p>
            <p>Tutto aggiornato ogni 2 secondi, direttamente dalla dashboard cliente.</p>
            <ul class="feature-list">
                <li>Velocità drone in km/h</li>
                <li>Livello batteria residua</li>
                <li>Distanza rimasta in km</li>
                <li>ETA in minuti</li>
            </ul>
        </div>
    </div>

    <div class="section reverse">
        <img src="assets/mappa.png" alt="Mappa live">
        <div class="section-text">
            <h2>Mappa interattiva</h2>
            <p>Visualizza in tempo reale il percorso del drone, il punto di ritiro e il punto di consegna su mappa OpenStreetMap.</p>
            <p>Il marker si muove fluidamente grazie all'animazione GPS interpolata, nessun salto brusco.</p>
            <ul class="feature-list">
                <li>Percorso tracciato in verde</li>
                <li>Punto di ritiro e consegna marcati</li>
                <li>Zoom automatico sul drone</li>
            </ul>
        </div>
    </div>
</div>

<div class="cta">
    <h2>Prova il tracking dal vivo</h2>
    <p>Crea un ordine e segui il tuo drone in tempo reale.</p>
    <a href="signup_customer.php" class="btn-hero btn-primary">Inizia ora</a>
</div>

<script>
const sections = document.querySelectorAll('.section');
function checkScroll() { sections.forEach(s => { if (s.getBoundingClientRect().top < window.innerHeight - 100) s.classList.add('visible'); }); }
window.addEventListener('scroll', checkScroll);
checkScroll();
</script>
</body>
</html>