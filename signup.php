<?php
require_once 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (name, username, email, password_hash)
            VALUES (:n, :u, :e, :p)
        ");
        $stmt->execute([
            ':n' => $name,
            ':u' => $username,
            ':e' => $email,
            ':p' => $password
        ]);

        $message = "Registrazione completata!";
    } catch (PDOException $e) {
        $message = "Errore: username o email già esistenti.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
        /* ============================
           SIGNUP PAGE TECH
        ============================ */

        .signup-container {
            width: 420px;
            margin: 80px auto;
            padding: 35px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #00eaff;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: 500;
        }

        .message.success {
            color: #00ff88;
        }

        .message.error {
            color: #ff4444;
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #00eaff;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link:hover {
            text-shadow: 0 0 6px #00eaff;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: 1px solid #00eaff;
            background: rgba(0, 234, 255, 0.04);
            color: #00eaff;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.25s;
        }

        button:hover {
            background: rgba(0, 234, 255, 0.12);
            box-shadow: 0 0 10px #00eaff55;
        }
    </style>
</head>
<body>

<div class="signup-container">

    <h1>Registrati</h1>

    <form method="POST">
        <input type="text" name="name" placeholder="Nome completo" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Registrati</button>
    </form>

    <a href="login.php" class="login-link">Hai già un account? Accedi</a>

    <?php if (!empty($message)): ?>
        <p class="message <?= strpos($message, 'completata') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

</div>

</body>
</html>
