<?php
require_once 'config.php';
session_start();

include "menu_operatore.php";
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Aggiungi Drone</title>

<style>
body {
    background:#0a0f1f;
    color:#e6f1ff;
    font-family:"Segoe UI", Arial;
}

.container {
    max-width:600px;
    margin:40px auto;
    background:rgba(255,255,255,0.04);
    padding:25px;
    border-radius:14px;
    border:1px solid #00eaff33;
    box-shadow:0 0 20px #00eaff22;
}

h1 {
    color:#00eaff;
    text-shadow:0 0 10px #00eaff55;
}

input, select {
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #00eaff33;
    background:rgba(255,255,255,0.05);
    color:#e6f1ff;
}

button {
    padding:12px 18px;
    background:rgba(0,234,255,0.1);
    border:1px solid #00eaff;
    color:#00eaff;
    border-radius:8px;
    cursor:pointer;
    transition:0.25s;
}

button:hover {
    background:rgba(0,234,255,0.2);
    box-shadow:0 0 10px #00eaff55;
}
</style>
</head>
<body>

<div class="container">
    <h1>Aggiungi Drone</h1>

    <form action="add_drone_process.php" method="POST">
        <label>Nome drone</label>
        <input type="text" name="name" required>

        <label>Batteria (%)</label>
        <input type="number" name="battery" min="0" max="100" required>

        <label>Latitudine</label>
        <input type="text" name="latitude" required>

        <label>Longitudine</label>
        <input type="text" name="longitude" required>

        <button type="submit">Aggiungi Drone</button>
    </form>
</div>

</body>
</html>


