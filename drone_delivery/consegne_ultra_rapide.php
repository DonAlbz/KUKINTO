<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Consegne Ultra-Rapide — DroneDelivery</title>
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
.steps { display:flex; flex-direction:column; gap:0; margin-bottom:80px; }
.step { display:flex; gap:28px; align-items:flex-start; padding:28px 0; border-bottom:1px solid rgba(0,234,255,0.06); opacity:0; transform:translateX(-30px); transition:0.7s ease; }
.step.visible { opacity:1; transform:translateX(0); }
.step-num { width:44px; height:44px; border-radius:50%; border:2px solid #00eaff; color:#00eaff; font-size:18px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 0 12px rgba(0,234,255,0.2); }
.step-content h3 { font-size:18px; color:#e6f1ff; margin-bottom:6px; }
.step-content p { font-size:14px; color:#8aaec8; line-height:1.7; }
.section { display:flex; align-items:center; gap:60px; margin-bottom:80px; opacity:0; transform:translateY(40px); transition:1s ease; }
.section.visible { opacity:1; transform:translateY(0); }
.section.reverse { flex-direction:row-reverse; }
.section img { width:45%; border-radius:16px; border:1px solid rgba(0,234,255,0.15); box-shadow:0 0 30px rgba(0,234,255,0.1); }
.section-text h2 { font-size:28px; color:#e6f1ff; margin-bottom:14px; }
.section-text p { font-size:15px; color:#8aaec8; line-height:1.8; margin-bottom:12px; }
.feature-list { list-style:none; margin-top:16px; }
.feature-list li { padding:8px 0; font-size:14px; color:#cfe8ff; border-bottom:1px solid rgba(0,234,255,0.06); display:flex; align-items:center; gap:10px; }
.feature-list li::before { content:'→'; color:#00eaff; font-weight:700; }
.stats-bar { display:flex; background:rgba(255,255,255,0.02); border:1px solid rgba(0,234,255,0.1); border-radius:16px; overflow:hidden; margin-bottom:80px; }
.stat-item { flex:1; padding:28px 20px; text-align:center; border-right:1px solid rgba(0,234,255,0.08); }
.stat-item:last-child { border-right:none; }
.stat-num { font-size:36px; font-weight:700; color:#00eaff; margin-bottom:4px; }
.stat-desc { font-size:13px; color:#5a7a99; }
.cta { text-align:center; padding:60px 40px; border-top:1px solid rgba(0,234,255,0.08); }
.cta h2 { font-size:32px; color:#e6f1ff; margin-bottom:10px; }
.cta p { color:#5a7a99; margin-bottom:28px; font-size:15px; }
.btn-hero { display:inline-block; padding:12px 28px; border-radius:10px; font-size:15px; font-weight:600; border:1px solid; transition:0.2s; }
.btn-primary { color:#0a0f1f; background:#00eaff; border-color:#00eaff; }
.btn-primary:hover { box-shadow:0 0 24px rgba(0,234,255,0.5); transform:translateY(-2px); }
@media(max-width:768px) { .section,.section.reverse{flex-direction:column} .section img{width:100%} .stats-bar{flex-direction:column} }
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
    <div class="hero-tag">⚡ Velocità</div>
    <h1 class="page-title">Consegne<br>ultra-rapide</h1>
    <p class="page-subtitle">I droni volano in linea retta, ignorano il traffico e non si fermano mai. Il risultato? Consegne fino a 5 volte più veloci di un corriere tradizionale.</p>

    <div class="stats-bar">
        <div class="stat-item"><div class="stat-num">< 15'</div><div class="stat-desc">Tempo medio di consegna</div></div>
        <div class="stat-item"><div class="stat-num">80 km/h</div><div class="stat-desc">Velocità di crociera</div></div>
        <div class="stat-item"><div class="stat-num">25 km</div><div class="stat-desc">Raggio operativo</div></div>
        <div class="stat-item"><div class="stat-num">10 kg</div><div class="stat-desc">Carico massimo</div></div>
    </div>

    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <div class="step-content">
                <h3>Crei l'ordine</h3>
                <p>Inserisci indirizzo di ritiro e consegna. Il sistema verifica automaticamente che siano all'interno di Brescia e calcola il percorso ottimale.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-content">
                <h3>Viene assegnato un drone</h3>
                <p>L'operatore assegna il drone disponibile più vicino al punto di ritiro, ottimizzando i tempi di attesa.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-content">
                <h3>Il drone parte</h3>
                <p>Il drone decolla e segue il percorso GPS ottimizzato in linea retta, senza fermarsi al traffico o ai semafori.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <div class="step-content">
                <h3>Consegna completata</h3>
                <p>Ricevi una notifica all'arrivo. Il drone torna automaticamente alla base e viene ricaricato per il prossimo ordine.</p>
            </div>
        </div>
    </div>

    <div class="section">
        <img src="assets/drone.png" alt="Drone in volo">
        <div class="section-text">
            <h2>Tecnologia di navigazione</h2>
            <p>I nostri droni utilizzano GPS centimetrico e sensori di ostacoli per volare in sicurezza a bassa quota su Brescia.</p>
            <p>Ogni volo è monitorato in tempo reale dal pannello operatore e conforme alle normative ENAC.</p>
            <ul class="feature-list">
                <li>GPS con precisione centimetrica</li>
                <li>Sensori anti-collisione</li>
                <li>Volo autonomo certificato ENAC</li>
                <li>Monitoraggio in tempo reale</li>
            </ul>
        </div>
    </div>
</div>

<div class="cta">
    <h2>Vuoi una consegna rapida?</h2>
    <p>Registrati e ricevi il tuo primo ordine in meno di 15 minuti.</p>
    <a href="signup_customer.php" class="btn-hero btn-primary">Inizia ora</a>
</div>

<script>
const els = document.querySelectorAll('.section, .step');
function checkScroll() { els.forEach(e => { if (e.getBoundingClientRect().top < window.innerHeight - 80) e.classList.add('visible'); }); }
window.addEventListener('scroll', checkScroll);
checkScroll();
</script>
</body>
</html>
