<?php
require_once 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Credenziali errate.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
        /* ============================
           LOGIN PAGE TECH
        ============================ */

        .login-container {
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

        .error-msg {
            text-align: center;
            color: #ff4444;
            margin-top: 10px;
            font-weight: 500;
        }

        .signup-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #00eaff;
            text-decoration: none;
            font-weight: 500;
        }

        .signup-link:hover {
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

<div class="login-container">

    <h1>Accedi</h1>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <a href="signup.php" class="signup-link">Non hai un account? Registrati</a>

    <?php if (!empty($message)): ?>
        <p class="error-msg"><?= $message ?></p>
    <?php endif; ?>

</div>

</body>
</html>
