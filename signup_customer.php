<?php
require_once 'config.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $message = "Le password non coincidono.";
    } else {

        $stmt = $pdo->prepare("SELECT id FROM customers WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute([':u' => $username, ':e' => $email]);

        if ($stmt->fetch()) {
            $message = "Username o email già esistenti.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO customers (username, email, password_hash) VALUES (:u, :e, :p)");
            $stmt->execute([
                ':u' => $username,
                ':e' => $email,
                ':p' => $hash
            ]);

            $_SESSION['customer_id'] = $pdo->lastInsertId();
            $_SESSION['customer_username'] = $username;

            header("Location: customer_dashboard.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrazione Cliente</title>
    <link rel="stylesheet" href="assets/tech.css">

</head>
<body>

<div class="auth-container">
    <h2>Registrati come Cliente</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm" placeholder="Conferma Password" required>
        <button type="submit">Registrati</button>
    </form>

    <p style="margin-top:15px; text-align:center;">
        <a href="login_customer.php">Hai già un account? Accedi</a>
    </p>

    <p style="color:red; text-align:center;"><?= $message ?></p>
</div>

</body>
</html>
