<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';

if (isset($_SESSION['operator_id'])) {
    header("Location: operatore_dashboard.php");
    exit;
}

$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $message = "Le password non coincidono.";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (name, username, email, password_hash, role)
                VALUES (:n, :u, :e, :p, 'operatore')
            ");
            $stmt->execute([':n' => $name, ':u' => $username, ':e' => $email, ':p' => $hash]);
            $success = true;
            $message = "Registrazione completata! Puoi ora accedere.";
        } catch (PDOException $e) {
            $message = "Username o email già esistenti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Registrazione Operatore — DroneDelivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body {
    background:#0a0f1f; color:#e6f1ff;
    font-family:"Segoe UI",Arial,sans-serif;
    min-height:100vh; display:flex; align-items:center; justify-content:center;
}
body::before {
    content:''; position:fixed; inset:0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(0,234,255,0.06) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(100,60,255,0.06) 0%, transparent 60%);
    pointer-events:none;
}
.card {
    background:rgba(255,255,255,0.03); border:1px solid rgba(0,234,255,0.15);
    border-radius:20px; padding:40px 36px; width:100%; max-width:420px;
    box-shadow:0 0 60px rgba(0,234,255,0.07);
}
.logo { display:flex; align-items:center; gap:10px; margin-bottom:28px; }
.logo-icon { width:38px; height:38px; background:rgba(0,234,255,0.1); border:1px solid rgba(0,234,255,0.3); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; }
.logo-text { font-size:16px; font-weight:600; color:#e6f1ff; }
.logo-sub  { font-size:12px; color:#5a7a99; }
.title    { font-size:20px; font-weight:600; color:#00eaff; margin-bottom:6px; }
.subtitle { font-size:13px; color:#5a7a99; margin-bottom:28px; }
.field { margin-bottom:14px; }
.field label { display:block; font-size:12px; color:#5a7a99; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase; }
.field input {
    width:100%; background:rgba(255,255,255,0.04); border:1px solid rgba(0,234,255,0.15);
    border-radius:10px; padding:11px 14px; color:#e6f1ff; font-size:14px; outline:none; transition:0.2s;
}
.field input:focus { border-color:rgba(0,234,255,0.5); background:rgba(0,234,255,0.04); box-shadow:0 0 0 3px rgba(0,234,255,0.08); }
.field input::placeholder { color:#3a5570; }
.btn-register {
    width:100%; padding:12px; background:rgba(0,234,255,0.1); border:1px solid rgba(0,234,255,0.35);
    border-radius:10px; color:#00eaff; font-size:14px; font-weight:600; cursor:pointer; transition:0.2s; margin-top:8px;
}
.btn-register:hover { background:rgba(0,234,255,0.18); box-shadow:0 0 20px rgba(0,234,255,0.15); }
.error   { margin-top:14px; padding:10px 14px; background:rgba(255,82,82,0.08); border:1px solid rgba(255,82,82,0.2); border-radius:8px; color:#ff7070; font-size:13px; text-align:center; }
.success { margin-top:14px; padding:10px 14px; background:rgba(0,230,118,0.08); border:1px solid rgba(0,230,118,0.2); border-radius:8px; color:#00e676; font-size:13px; text-align:center; }
.divider { display:flex; align-items:center; gap:10px; margin:20px 0; color:#2a3f55; font-size:12px; }
.divider::before, .divider::after { content:''; flex:1; height:1px; background:rgba(0,234,255,0.08); }
.login-link { text-align:center; font-size:13px; color:#5a7a99; }
.login-link a { color:#00eaff; text-decoration:none; font-weight:500; }
.login-link a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="card">
    <div class="logo">
        <div class="logo-icon">🚁</div>
        <div>
            <div class="logo-text">DroneDelivery</div>
            <div class="logo-sub">Pannello Operatore</div>
        </div>
    </div>
    <div class="title">Crea un account</div>
    <div class="subtitle">Registrati come operatore DroneDelivery</div>
    <form method="POST">
        <div class="field">
            <label>Nome completo</label>
            <input type="text" name="name" placeholder="Mario Rossi" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="field">
            <label>Username</label>
            <input type="text" name="username" placeholder="Scegli un username" required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" placeholder="La tua email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="field">
            <label>Conferma password</label>
            <input type="password" name="confirm" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-register">Registrati</button>
    </form>
    <?php if (!empty($message)): ?>
        <div class="<?= $success ? 'success' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="divider">oppure</div>
    <div class="login-link">
        Hai già un account? <a href="login_operatore.php">Accedi</a>
    </div>
</div>
</body>
</html>
