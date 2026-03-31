<?php
require_once 'config.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM customers WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer && password_verify($password, $customer['password_hash'])) {

        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_username'] = $customer['username'];

        header("Location: customer_dashboard.php");
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
    <title>Login Cliente</title>

    <style>
        body {
            margin: 0;
            background: #0a0f1f;
            color: #e6f1ff;
            font-family: "Segoe UI", Arial, sans-serif;

            display: flex;
            justify-content: center;
            align-items: center;

            height: 100vh;
        }

        .auth-container {
            width: 100%;
            max-width: 380px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
            box-shadow: 0 0 20px #00eaff22;
        }

        h2 {
            text-align: center;
            color: #00eaff;
            margin-bottom: 25px;
            font-size: 26px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 6px;
            border: 1px solid #00eaff44;
            background: rgba(255, 255, 255, 0.05);
            color: #e6f1ff;
            font-size: 15px;
        }

        input::placeholder {
            color: #9ac7d8;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #00eaff22;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.25s;
        }

        button:hover {
            background: #00eaff33;
            box-shadow: 0 0 10px #00eaff55;
        }

        a {
            color: #00eaff;
            text-decoration: none;
        }

        a:hover {
            text-shadow: 0 0 6px #00eaff;
        }

        .error {
            color: #ff4d4d;
            text-align: center;
            margin-top: 10px;
        }
    </style>

</head>
<body>

<div class="auth-container">
    <h2>Accedi come Cliente</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Accedi</button>
    </form>

    <p style="margin-top:15px; text-align:center;">
        <a href="signup_customer.php">Non hai un account? Registrati</a>
    </p>

    <?php if ($message): ?>
        <p class="error"><?= $message ?></p>
    <?php endif; ?>
</div>

</body>
</html>


